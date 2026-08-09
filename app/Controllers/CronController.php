<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Request;
use App\Core\Response;
use App\Core\Config;
use App\Core\Database;
use App\Repositories\NoteRepository;
use App\Repositories\ImageRepository;
use App\Services\AuditService;
use App\Services\ExpiryService;
use App\Services\ImageService;

/**
 * CronController – Automated cleanup of expired notes
 *
 * Endpoint: GET /cron/cleanup?key=CRON_SECRET
 * Set up a Hostinger cron job to call this URL every 30 minutes.
 */
class CronController
{
    public function cleanup(Request $request, Response $response): void
    {
        // Validate cron secret
        $key          = $request->query('key', '');
        $expectedKey  = Config::env('CRON_SECRET', '');

        if (!hash_equals($expectedKey, $key)) {
            $response->jsonError('Unauthorized.', 401);
            return;
        }

        $notes   = new NoteRepository();
        $images  = new ImageRepository();
        $audit   = new AuditService();
        $expiry  = new ExpiryService();

        $deleted  = 0;
        $errors   = 0;
        $start    = microtime(true);

        try {
            $expiredNotes = $notes->getExpiredNotes();

            foreach ($expiredNotes as $note) {
                try {
                    // Delete images from disk
                    $images->deleteBySlug($note->slug);

                    // Hard delete the note
                    $notes->hardDelete($note->slug);

                    $audit->log('note_auto_deleted', $note->slug, 'cron', null, [
                        'expired_at' => $note->expiresAt,
                    ]);

                    $deleted++;
                } catch (\Throwable $e) {
                    $errors++;
                }
            }

            // Also clean up old rate_limits records
            $db = Database::getInstance();
            $db->execute('DELETE FROM rate_limits WHERE window_start < DATE_SUB(NOW(), INTERVAL 2 HOUR)');

            // Clean up expired admin sessions
            $db->execute('DELETE FROM admin_sessions WHERE expires_at < NOW()');

            // Clean up old audit logs (keep 90 days)
            $db->execute('DELETE FROM audit_logs WHERE created_at < DATE_SUB(NOW(), INTERVAL 90 DAY)');

            $elapsed = round(microtime(true) - $start, 3);

            $response->jsonSuccess([
                'deleted'  => $deleted,
                'errors'   => $errors,
                'duration' => $elapsed . 's',
                'ran_at'   => date('Y-m-d H:i:s'),
            ], "Cleanup complete. $deleted notes deleted.");
        } catch (\Throwable $e) {
            $response->jsonError('Cleanup failed: ' . $e->getMessage(), 500);
        }
    }
}
