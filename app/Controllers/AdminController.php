<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Request;
use App\Core\Response;
use App\Core\Config;
use App\Core\Database;
use App\Repositories\NoteRepository;
use App\Services\AuditService;
use App\Services\CsrfService;
use App\Services\PasswordService;

/**
 * AdminController – Admin Panel
 *
 * Authentication via .env ADMIN_PASSWORD.
 * Sessions stored in admin_sessions table.
 */
class AdminController
{
    private ?NoteRepository $notes = null;
    private AuditService    $audit;
    private CsrfService     $csrf;

    public function __construct()
    {
        $this->audit = new AuditService();
        $this->csrf  = new CsrfService();
    }

    private function getNotes(): NoteRepository
    {
        return $this->notes ??= new NoteRepository();
    }

    // ─────────────────────────────────────────────
    // Login
    // ─────────────────────────────────────────────

    public function loginForm(Request $request, Response $response): void
    {
        // Already logged in?
        if ($this->isLoggedIn($request)) {
            $response->redirect('/admin/dashboard');
            return;
        }

        $response->view('admin.login', [
            'pageTitle' => 'Admin Login – Cabin',
            'csrfToken' => $this->csrf->getToken(),
            'error'     => $_SESSION['admin_login_error'] ?? null,
        ], 'minimal');

        unset($_SESSION['admin_login_error']);
    }

    public function login(Request $request, Response $response): void
    {
        $password       = $request->body('password', '');
        $adminPassword  = Config::env('ADMIN_PASSWORD', '');

        if (empty($adminPassword)) {
            $response->error(500, 'Admin password not configured.');
            return;
        }

        // Timing-safe comparison
        if (!hash_equals($adminPassword, $password)) {
            $this->audit->log('admin_login_failed', null, $request->ipHash(), $request->userAgent());

            // Store error in session for redirect
            $_SESSION['admin_login_error'] = 'Incorrect password. Please try again.';
            $response->redirect('/admin/login');
            return;
        }

        // Create session token
        $token     = bin2hex(random_bytes(32));
        $tokenHash = hash('sha256', $token);
        $lifetime  = (int) Config::env('ADMIN_SESSION_LIFETIME', 7200);
        $expiresAt = date('Y-m-d H:i:s', time() + $lifetime);

        $db = Database::getInstance();
        $db->execute(
            'INSERT INTO admin_sessions (token_hash, ip_hash, expires_at) VALUES (?, ?, ?)',
            [$tokenHash, $request->ipHash(), $expiresAt]
        );

        $_SESSION['admin_token'] = $token;

        $this->audit->log('admin_login', null, $request->ipHash(), $request->userAgent());

        $response->redirect('/admin/dashboard');
    }

    public function logout(Request $request, Response $response): void
    {
        $token = $_SESSION['admin_token'] ?? null;

        if ($token) {
            $tokenHash = hash('sha256', $token);
            $db = Database::getInstance();
            $db->execute('DELETE FROM admin_sessions WHERE token_hash = ?', [$tokenHash]);
        }

        unset($_SESSION['admin_token']);
        $this->audit->log('admin_logout', null, $request->ipHash(), $request->userAgent());

        $response->redirect('/admin/login');
    }

    // ─────────────────────────────────────────────
    // Dashboard
    // ─────────────────────────────────────────────

    public function dashboard(Request $request, Response $response): void
    {
        $stats        = $this->getNotes()->getStats();
        $recentNotes  = $this->getNotes()->getRecent(10);
        $recentLogs   = $this->audit->getRecent(15);
        $totalStorage = $this->getNotes()->getTotalStorageBytes();

        $response->view('admin.dashboard', [
            'pageTitle'    => 'Admin Dashboard – Cabin',
            'stats'        => $stats,
            'recentNotes'  => $recentNotes,
            'recentLogs'   => $recentLogs,
            'totalStorage' => $this->humanSize($totalStorage),
            'csrfToken'    => $this->csrf->getToken(),
        ], 'minimal');
    }

    // ─────────────────────────────────────────────
    // Notes List
    // ─────────────────────────────────────────────

    public function notes(Request $request, Response $response): void
    {
        $recentNotes = $this->getNotes()->getRecent(50);

        $response->view('admin.notes', [
            'pageTitle'  => 'All Notes – Admin – Cabin',
            'notes'      => $recentNotes,
            'csrfToken'  => $this->csrf->getToken(),
        ], 'minimal');
    }

    // ─────────────────────────────────────────────
    // Logs
    // ─────────────────────────────────────────────

    public function logs(Request $request, Response $response): void
    {
        $action = $request->query('action');
        $logs   = $this->audit->getRecent(100, $action ?: null);

        $response->view('admin.logs', [
            'pageTitle'    => 'Audit Logs – Admin – Cabin',
            'logs'         => $logs,
            'filterAction' => $action,
            'csrfToken'    => $this->csrf->getToken(),
        ], 'minimal');
    }

    // ─────────────────────────────────────────────
    // Admin Delete Note
    // ─────────────────────────────────────────────

    public function deleteNote(Request $request, Response $response): void
    {
        $slug = $request->param('slug');
        $note = $this->getNotes()->findBySlug($slug);

        if ($note) {
            $imageRepo = new \App\Repositories\ImageRepository();
            $imageRepo->deleteBySlug($slug);
            $this->getNotes()->hardDelete($slug);
            $this->audit->log('admin_note_deleted', $slug, $request->ipHash(), $request->userAgent());
        }

        if ($request->expectsJson()) {
            $response->jsonSuccess(null, 'Note deleted.');
        } else {
            $response->redirect('/admin/notes');
        }
    }

    // ─────────────────────────────────────────────
    // Helpers
    // ─────────────────────────────────────────────

    private function isLoggedIn(Request $request): bool
    {
        $token = $_SESSION['admin_token'] ?? null;
        if (!$token) return false;

        try {
            $db        = Database::getInstance();
            $tokenHash = hash('sha256', $token);

            $row = $db->fetchOne(
                'SELECT id FROM admin_sessions WHERE token_hash = ? AND expires_at > CURRENT_TIMESTAMP LIMIT 1',
                [$tokenHash]
            );

            return $row !== false;
        } catch (\Throwable $e) {
            return false;
        }
    }

    private function humanSize(int $bytes): string
    {
        if ($bytes < 1024)       return $bytes . ' B';
        if ($bytes < 1048576)    return round($bytes / 1024, 1) . ' KB';
        if ($bytes < 1073741824) return round($bytes / 1048576, 2) . ' MB';
        return round($bytes / 1073741824, 2) . ' GB';
    }
}
