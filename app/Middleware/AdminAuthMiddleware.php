<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Core\Request;
use App\Core\Response;
use App\Core\Config;
use App\Core\Database;

/**
 * AdminAuthMiddleware – Admin Panel Authentication
 *
 * Checks for a valid admin session token in the PHP session.
 * Session tokens are stored hashed in the admin_sessions table.
 */
class AdminAuthMiddleware
{
    public function handle(Request $request, Response $response, callable $next): void
    {
        $token = $_SESSION['admin_token'] ?? null;

        if (!$token || !$this->isValidSession($token, $request->ipHash())) {
            // Clear invalid session
            unset($_SESSION['admin_token']);

            if ($request->expectsJson()) {
                $response->jsonError('Admin authentication required.', 401);
                return;
            }

            $response->redirect('/admin/login');
            return;
        }

        $next();
    }

    private function isValidSession(string $token, string $ipHash): bool
    {
        try {
            $db       = Database::getInstance();
            $tokenHash = hash('sha256', $token);

            $row = $db->fetchOne(
                'SELECT id, ip_hash FROM admin_sessions
                 WHERE token_hash = ? AND expires_at > CURRENT_TIMESTAMP
                 LIMIT 1',
                [$tokenHash]
            );

            if (!$row) return false;

            // IP lock – session must be from same IP
            return hash_equals($row['ip_hash'], $ipHash);
        } catch (\Throwable) {
            return false;
        }
    }
}
