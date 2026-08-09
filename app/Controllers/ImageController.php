<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Request;
use App\Core\Response;
use App\Core\Config;
use App\Repositories\NoteRepository;
use App\Repositories\ImageRepository;
use App\Services\ImageService;
use App\Services\AuditService;

/**
 * ImageController – Image upload and serving
 */
class ImageController
{
    private NoteRepository  $notes;
    private ImageRepository $images;
    private ImageService    $imageService;
    private AuditService    $audit;

    public function __construct()
    {
        $this->notes        = new NoteRepository();
        $this->images       = new ImageRepository();
        $this->imageService = new ImageService();
        $this->audit        = new AuditService();
    }

    // ─────────────────────────────────────────────
    // Upload Image (POST /image/upload)
    // Images can be uploaded before note is saved (uses session temp store)
    // ─────────────────────────────────────────────

    public function upload(Request $request, Response $response): void
    {
        $noteSlug      = $request->body('note_slug', '');
        $uploadSession = $request->body('upload_session', '');
        $file          = $request->file('image');

        if (!$file) {
            $response->jsonError('No image file provided.', 400);
            return;
        }

        // If we have a real slug, note exists – attach directly
        if ($noteSlug && strlen($noteSlug) >= 8) {
            $note = $this->notes->findBySlug($noteSlug);
            if (!$note) {
                $response->jsonError('Note not found.', 404);
                return;
            }

            // Check max images
            $maxImages = (int) Config::env('MAX_IMAGES_PER_NOTE', 5);
            if ($this->images->countBySlug($noteSlug) >= $maxImages) {
                $response->jsonError("Maximum $maxImages images per note.", 400);
                return;
            }

            try {
                $imageData = $this->imageService->process($file, $noteSlug);
                $image     = $this->images->save($note->id, $noteSlug, $imageData);

                $response->jsonSuccess([
                    'id'            => $image->id,
                    'filename'      => $image->filename,
                    'original_name' => $image->originalName,
                    'size'          => $image->humanSize(),
                    'width'         => $image->width,
                    'height'        => $image->height,
                    'url'           => '/image/' . $noteSlug . '/' . $image->filename,
                ], 'Image uploaded successfully.');
            } catch (\InvalidArgumentException $e) {
                $response->jsonError($e->getMessage(), 422);
            } catch (\Throwable $e) {
                $response->jsonError('Image upload failed.', 500);
            }

            return;
        }

        // No slug yet – store in session until note is created
        if (!$uploadSession) {
            $response->jsonError('Upload session ID required.', 400);
            return;
        }

        // Validate first before storing
        try {
            $tempSlug  = 'temp_' . $uploadSession;
            $imageData = $this->imageService->process($file, $tempSlug);

            // Store metadata in session
            if (!isset($_SESSION['pending_images'])) {
                $_SESSION['pending_images'] = [];
            }
            $_SESSION['pending_images'][$uploadSession][] = $imageData;

            $response->jsonSuccess([
                'filename'      => $imageData['filename'],
                'original_name' => $imageData['original_name'],
                'size'          => $this->humanSize($imageData['size_bytes']),
                'width'         => $imageData['width'],
                'height'        => $imageData['height'],
            ], 'Image queued for upload.');
        } catch (\InvalidArgumentException $e) {
            $response->jsonError($e->getMessage(), 422);
        } catch (\Throwable $e) {
            $response->jsonError('Image upload failed.', 500);
        }
    }

    // ─────────────────────────────────────────────
    // Serve Image (GET /image/{slug}/{filename})
    // Serves images securely through PHP (not directly from webroot)
    // ─────────────────────────────────────────────

    public function serve(Request $request, Response $response): void
    {
        $slug     = $request->param('slug');
        $filename = $request->param('filename');

        // Validate filename – no path traversal
        if (!preg_match('/^[a-f0-9]{32}\.(jpg|jpeg|png|gif|webp)$/i', $filename)) {
            $response->error(404, 'Image not found.');
            return;
        }

        // Check note exists and is accessible
        $note = $this->notes->findBySlug($slug);
        if (!$note || $note->isDeleted() || $note->isExpired) {
            $response->error(404, 'Image not found.');
            return;
        }

        // Check image record exists
        $image = $this->images->findByFilename($slug, $filename);
        if (!$image) {
            $response->error(404, 'Image not found.');
            return;
        }

        $absPath = $this->imageService->getAbsolutePath($image->storagePath);
        if (!file_exists($absPath)) {
            $response->error(404, 'Image file not found.');
            return;
        }

        // Serve the file
        $mimeType = mime_content_type($absPath);
        header('Content-Type: ' . $mimeType);
        header('Content-Length: ' . filesize($absPath));
        header('Cache-Control: private, max-age=3600');
        header('X-Content-Type-Options: nosniff');
        header('Content-Disposition: inline; filename="' . addslashes($image->originalName) . '"');
        readfile($absPath);
        exit;
    }

    private function humanSize(int $bytes): string
    {
        if ($bytes < 1024)    return $bytes . ' B';
        if ($bytes < 1048576) return round($bytes / 1024, 1) . ' KB';
        return round($bytes / 1048576, 2) . ' MB';
    }
}
