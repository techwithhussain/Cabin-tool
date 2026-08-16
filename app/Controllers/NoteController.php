<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Request;
use App\Core\Response;
use App\Core\Config;
use App\Repositories\NoteRepository;
use App\Repositories\ImageRepository;
use App\Services\ExpiryService;
use App\Services\PasswordService;
use App\Services\CsrfService;
use App\Services\AuditService;

/**
 * NoteController – Core note CRUD operations
 */
class NoteController
{
    private ?NoteRepository  $notes = null;
    private ?ImageRepository $images = null;
    private ExpiryService   $expiry;
    private PasswordService $password;
    private CsrfService     $csrf;
    private AuditService    $audit;

    public function __construct()
    {
        $this->expiry   = new ExpiryService();
        $this->password = new PasswordService();
        $this->csrf     = new CsrfService();
        $this->audit    = new AuditService();
    }

    private function getNotes(): NoteRepository
    {
        return $this->notes ??= new NoteRepository();
    }

    private function getImages(): ImageRepository
    {
        return $this->images ??= new ImageRepository();
    }

    // ─────────────────────────────────────────────
    // Workspace (Create Note Form)
    // ─────────────────────────────────────────────

    public function workspace(Request $request, Response $response): void
    {
        $response->view('workspace.create', [
            'pageTitle'     => 'Create Secure Note – Cabin',
            'pageDesc'      => 'Create a private, encrypted note. Set expiry, add password, upload images.',
            'csrfToken'     => $this->csrf->getToken(),
            'expiryOptions' => ExpiryService::OPTIONS,
            'maxImages'     => (int) Config::env('MAX_IMAGES_PER_NOTE', 5),
            'maxNoteLen'    => (int) Config::env('MAX_NOTE_LENGTH', 50000),
            'maxUploadMB'   => round(Config::env('MAX_UPLOAD_SIZE', 10485760) / 1048576, 0),
            'customSlug'    => '',
        ], 'minimal');
    }

    /**
     * Direct Custom Slug handler (e.g. cabinn.in/hello)
     * If note exists -> show note (or password gate)
     * If not exists -> open editor with custom slug pre-filled!
     */
    public function handleDirectSlug(Request $request, Response $response): void
    {
        $slug = trim($request->param('slug', ''));
        $reserved = ['create', 'note', 'admin', 'image', 'cron', 'login', 'dashboard', 'api', 'logout', 'setup', 'uploads', 'assets', 'favicon.ico', 'robots.txt'];

        if (in_array(strtolower($slug), $reserved, true)) {
            $response->error(404, 'Page not found');
            return;
        }

        $note = $this->getNotes()->findBySlug($slug);

        // If note exists and is active (not deleted or expired)
        if ($note && !$note->isDeleted() && !$note->isExpired && !$this->expiry->isExpired($note->expiresAt)) {
            $this->view($request, $response);
            return;
        }

        // If note does not exist -> Open workspace editor pre-filled with this slug!
        $response->view('workspace.create', [
            'pageTitle'     => "Create Note – cabinn.in/$slug",
            'pageDesc'      => 'Create a private, encrypted note. Set expiry, add password, upload images.',
            'csrfToken'     => $this->csrf->getToken(),
            'expiryOptions' => ExpiryService::OPTIONS,
            'maxImages'     => (int) Config::env('MAX_IMAGES_PER_NOTE', 5),
            'maxNoteLen'    => (int) Config::env('MAX_NOTE_LENGTH', 50000),
            'maxUploadMB'   => round(Config::env('MAX_UPLOAD_SIZE', 10485760) / 1048576, 0),
            'customSlug'    => $slug,
        ], 'minimal');
    }

    // ─────────────────────────────────────────────
    // Create Note (POST)
    // ─────────────────────────────────────────────

    public function create(Request $request, Response $response): void
    {
        $content       = trim($request->body('content', ''));
        $expiryOption  = $request->body('expiry', '24h');
        $password      = $request->body('password', '');
        $burnAfterRead = (bool) $request->body('burn_after_read', false);
        $customSlug    = trim($request->body('custom_slug', ''));

        // ── Validation ──────────────────────────────
        $errors = [];

        if (empty($content)) {
            $errors[] = 'Note content cannot be empty.';
        }

        $maxLen = (int) Config::env('MAX_NOTE_LENGTH', 50000);
        if (strlen($content) > $maxLen) {
            $errors[] = "Note content exceeds maximum length of $maxLen characters.";
        }

        if (!$this->expiry->isValid($expiryOption)) {
            $expiryOption = '24h';
        }

        if (!empty($password) && !$this->password->validate($password)) {
            $errors[] = 'Password must be between 1 and 128 characters.';
        }

        // Custom URL Slug Validation & Availability Check
        if (!empty($customSlug)) {
            if (!preg_match('/^[a-zA-Z0-9_-]{3,24}$/', $customSlug)) {
                $errors[] = 'Custom URL must be 3-24 characters long and contain only letters, numbers, hyphens, or underscores.';
            } else {
                $reserved = ['create', 'note', 'admin', 'image', 'cron', 'login', 'dashboard', 'api', 'logout', 'setup', 'uploads', 'assets', 'favicon'];
                if (in_array(strtolower($customSlug), $reserved, true)) {
                    $errors[] = 'This custom URL path is reserved. Please choose another one.';
                } elseif ($this->getNotes()->findBySlug($customSlug) !== null) {
                    $errors[] = 'This Custom URL is already taken. Please choose another one.';
                }
            }
        }

        if (!empty($errors)) {
            $response->jsonError(implode(' ', $errors), 422, $errors);
            return;
        }

        // ── Create Note ──────────────────────────────
        try {
            $result = $this->getNotes()->create([
                'content'         => $content,
                'expires_at'      => $this->expiry->toDateTime($expiryOption),
                'password'        => $password,
                'burn_after_read' => $burnAfterRead,
                'custom_slug'     => $customSlug,
                'creator_ip_hash' => $request->ipHash(),
            ]);

            $note       = $result['note'];
            $ownerToken = $result['owner_token'];
            $slug       = $result['slug'];

            // Handle pending image uploads (images uploaded before note creation)
            $pendingImages = $_SESSION['pending_images'][$request->body('upload_session', '')] ?? [];
            foreach ($pendingImages as $imageData) {
                // Move from temp slug to real note slug
                $this->getImages()->save($note->id, $slug, $imageData);
            }
            unset($_SESSION['pending_images'][$request->body('upload_session', '')]);

            // Audit log
            $this->audit->log('note_created', $slug, $request->ipHash(), $request->userAgent(), [
                'has_password'    => !empty($password),
                'burn_after_read' => $burnAfterRead,
                'expiry'          => $expiryOption,
            ]);

            // Grant creator session access for subsequent editing/auto-saves
            if (!empty($password)) {
                $sessionKey = 'note_access_' . hash('sha256', $slug);
                $_SESSION[$sessionKey] = true;
            }

            $appUrl = rtrim(Config::env('APP_URL', ''), '/');

            $response->jsonSuccess([
                'slug'        => $slug,
                'url'         => "$appUrl/$slug",
                'owner_token' => $ownerToken,
                'expires_at'  => $note->expiresAt,
                'expiry_label'=> ExpiryService::OPTIONS[$expiryOption] ?? 'Never',
            ], 'Note created successfully.');
        } catch (\Throwable $e) {
            $this->audit->log('note_create_error', null, $request->ipHash(), $request->userAgent(), [
                'error' => $e->getMessage()
            ]);

            $msg = 'Failed to create note: ' . $e->getMessage();
            $response->jsonError($msg, 500);
        }
    }

    // ─────────────────────────────────────────────
    // View Note (GET)
    // ─────────────────────────────────────────────

    public function view(Request $request, Response $response): void
    {
        $slug = $request->param('slug');
        $note = $this->getNotes()->findBySlug($slug);

        // Not found or deleted
        if (!$note || $note->isDeleted()) {
            $response->view('note.expired', [
                'pageTitle' => 'Note Not Found – Cabin',
                'reason'    => 'deleted',
            ], 'minimal');
            return;
        }

        // Check expiry
        if ($note->isExpired || $this->expiry->isExpired($note->expiresAt)) {
            if (!$note->isExpired) {
                $this->getNotes()->markExpired($slug);
            }
            $response->view('note.expired', [
                'pageTitle' => 'Note Expired – Cabin',
                'reason'    => 'expired',
            ], 'minimal');
            return;
        }

        // Password gate – show form if password protected and not yet verified
        if ($note->isPasswordProtected()) {
            $sessionKey = 'note_access_' . hash('sha256', $slug);
            if (!isset($_SESSION[$sessionKey]) || $_SESSION[$sessionKey] !== true) {
                $response->view('note.password', [
                    'pageTitle' => 'Protected Note – Cabin',
                    'slug'      => $slug,
                    'csrfToken' => $this->csrf->getToken(),
                ], 'minimal');
                return;
            }
        }

        // Track view
        $this->getNotes()->incrementViews($slug);
        $this->audit->log('note_viewed', $slug, $request->ipHash(), $request->userAgent());

        // Decrypt content
        $content = $this->getNotes()->decryptContent($note);
        $images  = $this->getImages()->getBySlug($slug);

        // Handle burn-after-read
        if ($note->burnAfterRead) {
            $this->getNotes()->softDelete($slug);
            $this->getImages()->deleteBySlug($slug);
            $this->audit->log('note_burned', $slug, $request->ipHash(), $request->userAgent());
        }

        $response->view('note.view', [
            'pageTitle'        => 'Secure Note – Cabin',
            'pageDesc'         => 'Private encrypted note shared via Cabin.',
            'note'             => $note,
            'content'          => $content,
            'images'           => $images,
            'remainingSeconds' => $this->expiry->remainingSeconds($note->expiresAt),
            'humanRemaining'   => $this->expiry->humanRemaining($note->expiresAt),
            'burnAfterRead'    => $note->burnAfterRead,
            'slug'             => $slug,
            'appUrl'           => rtrim(Config::env('APP_URL', ''), '/'),
        ], 'minimal');
    }

    // ─────────────────────────────────────────────
    // Verify Password (POST)
    // ─────────────────────────────────────────────

    public function verifyPassword(Request $request, Response $response): void
    {
        $slug     = $request->param('slug');
        $password = $request->body('password', '');
        $note     = $this->getNotes()->findBySlug($slug);

        if (!$note || $note->isDeleted() || $note->isExpired) {
            $response->jsonError('Note not found or expired.', 404);
            return;
        }

        if (!$note->isPasswordProtected()) {
            // No password needed – grant access
            $sessionKey = 'note_access_' . hash('sha256', $slug);
            $_SESSION[$sessionKey] = true;
            $response->jsonSuccess(null, 'Access granted.');
            return;
        }

        if ($this->password->verify($password, $note->passwordHash)) {
            // Store access in session
            $sessionKey = 'note_access_' . hash('sha256', $slug);
            $_SESSION[$sessionKey] = true;

            $this->audit->log('password_ok', $slug, $request->ipHash(), $request->userAgent());
            $response->jsonSuccess(null, 'Password correct. Redirecting...');
        } else {
            $this->audit->log('password_failed', $slug, $request->ipHash(), $request->userAgent());
            $response->jsonError('Incorrect password. Please try again.', 401);
        }
    }

    // ─────────────────────────────────────────────
    // Update Note Content (POST)
    // ─────────────────────────────────────────────

    public function update(Request $request, Response $response): void
    {
        $slug    = $request->param('slug');
        $content = trim($request->body('content', ''));
        $note    = $this->getNotes()->findBySlug($slug);

        if (!$note || $note->isDeleted() || $note->isExpired) {
            $response->jsonError('Note not found or expired.', 404);
            return;
        }

        // Password protection gate check
        if ($note->isPasswordProtected()) {
            $sessionKey = 'note_access_' . hash('sha256', $slug);
            if (!isset($_SESSION[$sessionKey]) || $_SESSION[$sessionKey] !== true) {
                $response->jsonError('Password verification required before editing.', 401);
                return;
            }
        }

        if (empty($content)) {
            $response->jsonError('Note content cannot be empty.', 422);
            return;
        }

        $maxLen = (int) Config::env('MAX_NOTE_LENGTH', 50000);
        if (strlen($content) > $maxLen) {
            $response->jsonError("Note content exceeds maximum length of $maxLen characters.", 422);
            return;
        }

        $this->getNotes()->updateContent($slug, $content);
        $this->audit->log('note_updated', $slug, $request->ipHash(), $request->userAgent());

        $response->jsonSuccess(['content' => $content], 'Note updated successfully!');
    }

    // ─────────────────────────────────────────────
    // Delete Note (POST with owner token)
    // ─────────────────────────────────────────────

    public function delete(Request $request, Response $response): void
    {
        $slug       = $request->param('slug');
        $ownerToken = $request->body('owner_token', '');
        $note       = $this->getNotes()->findBySlug($slug);

        if (!$note) {
            $response->jsonError('Note not found.', 404);
            return;
        }

        if (!$this->getNotes()->verifyOwnerToken($note, $ownerToken)) {
            $this->audit->log('delete_unauthorized', $slug, $request->ipHash(), $request->userAgent());
            $response->jsonError('Invalid owner token.', 403);
            return;
        }

        $this->getNotes()->softDelete($slug);
        $this->getImages()->deleteBySlug($slug);
        $this->audit->log('note_deleted', $slug, $request->ipHash(), $request->userAgent());

        $response->jsonSuccess(null, 'Note deleted successfully.');
    }
}
