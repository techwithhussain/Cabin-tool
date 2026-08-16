<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Core\Database;
use App\Models\Note;
use App\Services\EncryptionService;
use App\Services\TokenService;
use App\Services\PasswordService;

/**
 * NoteRepository – All database operations for notes
 *
 * Single source of truth for note persistence.
 * Encryption/decryption happens here.
 */
class NoteRepository
{
    private Database          $db;
    private EncryptionService $encryption;
    private TokenService      $token;
    private PasswordService   $password;

    public function __construct()
    {
        $this->db         = Database::getInstance();
        $this->encryption = new EncryptionService();
        $this->token      = new TokenService();
        $this->password   = new PasswordService();
    }

    // ─────────────────────────────────────────────
    // Create
    // ─────────────────────────────────────────────

    /**
     * Create a new note
     *
     * @return array{note: Note, owner_token: string, slug: string}
     */
    public function create(array $data): array
    {
        // Encrypt content
        $encrypted = $this->encryption->encrypt($data['content']);

        // Generate or use custom unique slug
        $slug = !empty($data['custom_slug']) ? trim($data['custom_slug']) : $this->token->generateSlug();

        // Hash owner token
        $ownerTokenData = $this->token->generateOwnerToken();

        // Hash password if provided
        $passwordHash = null;
        if (!empty($data['password'])) {
            $passwordHash = $this->password->hash($data['password']);
        }

        $this->db->execute(
            'INSERT INTO notes
             (slug, content_encrypted, content_iv, content_tag, password_hash,
              expires_at, burn_after_read, creator_ip_hash, owner_token_hash, created_at, updated_at)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)',
            [
                $slug,
                $encrypted['encrypted'],
                $encrypted['iv'],
                $encrypted['tag'],
                $passwordHash,
                $data['expires_at'] ?? null,
                $data['burn_after_read'] ? 1 : 0,
                $data['creator_ip_hash'],
                $ownerTokenData['hash'],
            ]
        );

        $id = (int) $this->db->lastInsertId();

        $note = new Note(
            id: $id,
            slug: $slug,
            contentEncrypted: $encrypted['encrypted'],
            contentIv: $encrypted['iv'],
            contentTag: $encrypted['tag'],
            passwordHash: $passwordHash,
            expiresAt: $data['expires_at'] ?? null,
            isExpired: false,
            burnAfterRead: (bool) ($data['burn_after_read'] ?? false),
            viewCount: 0,
            creatorIpHash: $data['creator_ip_hash'],
            ownerTokenHash: $ownerTokenData['hash'],
            createdAt: date('Y-m-d H:i:s'),
            updatedAt: date('Y-m-d H:i:s'),
            deletedAt: null
        );

        return [
            'note'        => $note,
            'owner_token' => $ownerTokenData['token'],
            'slug'        => $slug,
        ];
    }

    // ─────────────────────────────────────────────
    // Read
    // ─────────────────────────────────────────────

    public function findBySlug(string $slug): ?Note
    {
        $row = $this->db->fetchOne(
            'SELECT * FROM notes WHERE slug = ? AND deleted_at IS NULL LIMIT 1',
            [$slug]
        );

        return $row ? Note::fromRow($row) : null;
    }

    public function findById(int $id): ?Note
    {
        $row = $this->db->fetchOne(
            'SELECT * FROM notes WHERE id = ? LIMIT 1',
            [$id]
        );

        return $row ? Note::fromRow($row) : null;
    }

    /**
     * Decrypt and return note content
     */
    public function decryptContent(Note $note): string
    {
        return $this->encryption->decrypt(
            $note->contentEncrypted,
            $note->contentIv,
            $note->contentTag
        );
    }

    /**
     * Verify the owner token for delete operations
     */
    public function verifyOwnerToken(Note $note, string $token): bool
    {
        return hash_equals($note->ownerTokenHash, $this->token->hashToken($token));
    }

    // ─────────────────────────────────────────────
    // Update
    // ─────────────────────────────────────────────

    public function incrementViews(string $slug): void
    {
        $this->db->execute(
            'UPDATE notes SET view_count = view_count + 1, updated_at = CURRENT_TIMESTAMP WHERE slug = ?',
            [$slug]
        );
    }

    public function markExpired(string $slug): void
    {
        $this->db->execute(
            'UPDATE notes SET is_expired = 1, updated_at = CURRENT_TIMESTAMP WHERE slug = ?',
            [$slug]
        );
    }

    public function updateContent(string $slug, string $newContent): void
    {
        $encrypted = $this->encryption->encrypt($newContent);
        $this->db->execute(
            'UPDATE notes
             SET content_encrypted = ?, content_iv = ?, content_tag = ?, updated_at = CURRENT_TIMESTAMP
             WHERE slug = ? AND deleted_at IS NULL',
            [
                $encrypted['encrypted'],
                $encrypted['iv'],
                $encrypted['tag'],
                $slug,
            ]
        );
    }

    public function updateNote(
        string $slug,
        string $newContent,
        ?string $password = null,
        ?string $expiry = null,
        ?bool $burnAfterRead = null
    ): void {
        $encrypted = $this->encryption->encrypt($newContent);
        
        $fields = [
            'content_encrypted = ?',
            'content_iv = ?',
            'content_tag = ?',
            'updated_at = CURRENT_TIMESTAMP',
        ];
        $params = [
            $encrypted['encrypted'],
            $encrypted['iv'],
            $encrypted['tag'],
        ];

        if ($password !== null && $password !== '') {
            $fields[] = 'password_hash = ?';
            $params[] = $this->password->hash($password);
        }

        if ($expiry !== null && $expiry !== '') {
            $expiryService = new \App\Services\ExpiryService();
            $fields[] = 'expires_at = ?';
            $params[] = $expiryService->calculateExpiresAt($expiry);
        }

        if ($burnAfterRead !== null) {
            $fields[] = 'burn_after_read = ?';
            $params[] = $burnAfterRead ? 1 : 0;
        }

        $setClause = implode(', ', $fields);
        $params[] = $slug;

        $this->db->execute(
            "UPDATE notes SET $setClause WHERE slug = ? AND deleted_at IS NULL",
            $params
        );
    }

    // ─────────────────────────────────────────────
    // Delete
    // ─────────────────────────────────────────────

    /**
     * Soft-delete a note (sets deleted_at)
     */
    public function softDelete(string $slug): void
    {
        $this->db->execute(
            'UPDATE notes SET deleted_at = CURRENT_TIMESTAMP, updated_at = CURRENT_TIMESTAMP WHERE slug = ?',
            [$slug]
        );
    }

    /**
     * Hard-delete a note (permanent)
     */
    public function hardDelete(string $slug): void
    {
        $this->db->execute('DELETE FROM notes WHERE slug = ?', [$slug]);
    }

    // ─────────────────────────────────────────────
    // Admin / Cron
    // ─────────────────────────────────────────────

    /**
     * Get all expired but not yet cleaned up notes
     * @return Note[]
     */
    public function getExpiredNotes(): array
    {
        $rows = $this->db->fetchAll(
            'SELECT * FROM notes
             WHERE expires_at IS NOT NULL
               AND expires_at < CURRENT_TIMESTAMP
               AND deleted_at IS NULL
             ORDER BY expires_at ASC
             LIMIT 500'
        );

        return array_map(fn($row) => Note::fromRow($row), $rows);
    }

    /**
     * Statistics for admin dashboard
     */
    public function getStats(): array
    {
        return [
            'total'   => $this->db->fetchOne('SELECT COUNT(*) as c FROM notes WHERE deleted_at IS NULL')['c'] ?? 0,
            'active'  => $this->db->fetchOne('SELECT COUNT(*) as c FROM notes WHERE deleted_at IS NULL AND is_expired = 0 AND (expires_at IS NULL OR expires_at > CURRENT_TIMESTAMP)')['c'] ?? 0,
            'expired' => $this->db->fetchOne('SELECT COUNT(*) as c FROM notes WHERE is_expired = 1 OR (expires_at IS NOT NULL AND expires_at < CURRENT_TIMESTAMP)')['c'] ?? 0,
            'deleted' => $this->db->fetchOne('SELECT COUNT(*) as c FROM notes WHERE deleted_at IS NOT NULL')['c'] ?? 0,
        ];
    }

    /**
     * Get recent notes for admin panel
     */
    public function getRecent(int $limit = 20): array
    {
        return $this->db->fetchAll(
            'SELECT id, slug, expires_at, is_expired, burn_after_read, view_count, created_at, deleted_at
             FROM notes ORDER BY created_at DESC LIMIT ?',
            [$limit]
        );
    }

    /**
     * Get total storage used by images (via note_images join)
     */
    public function getTotalStorageBytes(): int
    {
        $row = $this->db->fetchOne('SELECT COALESCE(SUM(size_bytes), 0) as total FROM note_images');
        return (int) ($row['total'] ?? 0);
    }
}
