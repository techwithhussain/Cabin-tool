<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Database;
use App\Core\Config;

/**
 * RateLimitService – Database-backed Sliding Window Rate Limiting
 *
 * Tracks request counts per IP+action within a time window.
 * Compatible with shared hosting (no Redis/APCu needed).
 */
class RateLimitService
{
    private Database $db;

    /** Limits config: [action => [max_attempts, window_seconds]] */
    private array $limits;

    public function __construct()
    {
        $this->db = Database::getInstance();

        $this->limits = [
            'create'   => [(int) Config::env('RATE_LIMIT_CREATE', 10),   (int) Config::env('RATE_LIMIT_WINDOW', 3600)],
            'view'     => [(int) Config::env('RATE_LIMIT_VIEW', 60),     (int) Config::env('RATE_LIMIT_WINDOW', 3600)],
            'password' => [(int) Config::env('RATE_LIMIT_PASSWORD', 5),  (int) Config::env('RATE_LIMIT_PASSWORD_WINDOW', 900)],
            'admin'    => [5, 300], // 5 attempts per 5 minutes
        ];
    }

    /**
     * Check if an IP is rate-limited for a given action.
     *
     * @return bool True if ALLOWED, false if RATE LIMITED
     */
    public function check(string $ipHash, string $action): bool
    {
        [$maxAttempts, $windowSeconds] = $this->limits[$action] ?? [60, 3600];

        $row = $this->db->fetchOne(
            'SELECT id, attempts, window_start, blocked_until FROM rate_limits WHERE ip_hash = ? AND action = ?',
            [$ipHash, $action]
        );

        $now = new \DateTimeImmutable('now', new \DateTimeZone('UTC'));

        if ($row === false) {
            // First request – insert new record
            $this->db->execute(
                'INSERT INTO rate_limits (ip_hash, action, attempts, window_start) VALUES (?, ?, 1, CURRENT_TIMESTAMP)',
                [$ipHash, $action]
            );
            return true;
        }

        // Check if blocked
        if ($row['blocked_until'] !== null) {
            $blockedUntil = new \DateTimeImmutable($row['blocked_until'], new \DateTimeZone('UTC'));
            if ($now < $blockedUntil) {
                return false;
            }
        }

        $windowStart = new \DateTimeImmutable($row['window_start'], new \DateTimeZone('UTC'));
        $elapsed     = $now->getTimestamp() - $windowStart->getTimestamp();

        if ($elapsed > $windowSeconds) {
            // Window expired – reset
            $this->db->execute(
                'UPDATE rate_limits SET attempts = 1, window_start = CURRENT_TIMESTAMP, blocked_until = NULL WHERE id = ?',
                [$row['id']]
            );
            return true;
        }

        if ($row['attempts'] >= $maxAttempts) {
            // Block the IP for the remainder of the window
            $blockedUntil = $now->modify("+$windowSeconds seconds")->format('Y-m-d H:i:s');
            $this->db->execute(
                'UPDATE rate_limits SET blocked_until = ? WHERE id = ?',
                [$blockedUntil, $row['id']]
            );
            return false;
        }

        // Increment attempts
        $this->db->execute(
            'UPDATE rate_limits SET attempts = attempts + 1 WHERE id = ?',
            [$row['id']]
        );

        return true;
    }

    /**
     * Get seconds remaining in a block
     */
    public function getRetryAfter(string $ipHash, string $action): int
    {
        $row = $this->db->fetchOne(
            'SELECT blocked_until FROM rate_limits WHERE ip_hash = ? AND action = ?',
            [$ipHash, $action]
        );

        if (!$row || !$row['blocked_until']) return 0;

        $blockedUntil = new \DateTimeImmutable($row['blocked_until'], new \DateTimeZone('UTC'));
        $now          = new \DateTimeImmutable('now', new \DateTimeZone('UTC'));
        $diff         = $blockedUntil->getTimestamp() - $now->getTimestamp();

        return max(0, $diff);
    }

    /**
     * Clean up old records (run periodically)
     */
    private function cleanup(): void
    {
        // Only clean up occasionally to avoid DB hits on every request
        if (random_int(1, 50) !== 1) return;

        $this->db->execute(
            'DELETE FROM rate_limits WHERE window_start < CURRENT_TIMESTAMP AND (blocked_until IS NULL OR blocked_until < CURRENT_TIMESTAMP)'
        );
    }

    /**
     * Reset rate limit for a specific IP + action (admin tool)
     */
    public function reset(string $ipHash, string $action): void
    {
        $this->db->execute(
            'DELETE FROM rate_limits WHERE ip_hash = ? AND action = ?',
            [$ipHash, $action]
        );
    }
}
