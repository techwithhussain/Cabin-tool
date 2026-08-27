<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Database;

/**
 * AuditService – Security Audit Logging
 *
 * Records all sensitive actions to the audit_logs table.
 * Non-blocking: errors in audit logging should never fail a request.
 */
class AuditService
{
    private ?Database $db = null;

    public function __construct()
    {
    }

    private function getDb(): ?Database
    {
        if ($this->db === null) {
            try {
                $this->db = Database::getInstance();
            } catch (\Throwable $e) {
                return null;
            }
        }
        return $this->db;
    }

    /**
     * Log an audit event
     */
    public function log(
        string $action,
        ?string $noteSlug,
        string $ipHash,
        ?string $userAgent = null,
        array $metadata = []
    ): void {
        try {
            $db = $this->getDb();
            if (!$db) return;

            $db->execute(
                'INSERT INTO audit_logs (action, note_slug, ip_hash, user_agent, metadata_json, created_at)
                 VALUES (?, ?, ?, ?, ?, CURRENT_TIMESTAMP)',
                [
                    $action,
                    $noteSlug,
                    $ipHash,
                    $userAgent ? substr($userAgent, 0, 512) : null,
                    !empty($metadata) ? json_encode($metadata) : null,
                ]
            );
        } catch (\Throwable) {
            // Silently fail – audit logging must never break the application
        }
    }

    /**
     * Get recent audit logs for admin dashboard
     */
    public function getRecent(int $limit = 50, ?string $action = null): array
    {
        $db = $this->getDb();
        if (!$db) return [];

        $limit = max(1, min(500, $limit));
        $where  = $action ? 'WHERE action = ?' : '';
        $params = $action ? [$action] : [];

        return $db->fetchAll(
            "SELECT * FROM audit_logs $where ORDER BY created_at DESC LIMIT {$limit}",
            $params
        );
    }

    /**
     * Count events by action type for the last N days
     */
    public function countByAction(int $days = 7): array
    {
        $db = $this->getDb();
        if (!$db) return [];

        return $db->fetchAll(
            'SELECT action, COUNT(*) as count FROM audit_logs
             GROUP BY action ORDER BY count DESC'
        );
    }
}
