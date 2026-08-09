<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Database;

/**
 * TokenService – Secure URL Slug Generation
 *
 * Generates cryptographically random, URL-safe slugs.
 * Checks DB for collisions (extremely rare but handled).
 * Also generates owner delete tokens.
 */
class TokenService
{
    private const SLUG_LENGTH  = 12;
    private const TOKEN_LENGTH = 32;
    private const ALPHABET     = 'abcdefghijkmnpqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ23456789';
    private const MAX_RETRIES  = 10;

    private Database $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Generate a unique note slug with collision checking
     */
    public function generateSlug(): string
    {
        for ($i = 0; $i < self::MAX_RETRIES; $i++) {
            $slug = $this->randomString(self::SLUG_LENGTH);

            if (!$this->slugExists($slug)) {
                return $slug;
            }
        }

        // If all retries fail (astronomically unlikely), use longer slug
        return $this->randomString(self::SLUG_LENGTH + 4);
    }

    /**
     * Generate an owner token (for manual note deletion)
     * Returns both the raw token (give to user) and the hash (store in DB)
     *
     * @return array{token: string, hash: string}
     */
    public function generateOwnerToken(): array
    {
        $token = bin2hex(random_bytes(self::TOKEN_LENGTH));
        $hash  = hash('sha256', $token);

        return ['token' => $token, 'hash' => $hash];
    }

    /**
     * Hash an existing owner token for DB lookup
     */
    public function hashToken(string $token): string
    {
        return hash('sha256', $token);
    }

    /**
     * Generate a cron/admin token
     */
    public function generateSecret(int $bytes = 32): string
    {
        return bin2hex(random_bytes($bytes));
    }

    // ─────────────────────────────────────────────
    // Internals
    // ─────────────────────────────────────────────

    private function randomString(int $length): string
    {
        $alphabetLen = strlen(self::ALPHABET);
        $result = '';

        // Use random_bytes for cryptographic randomness
        $randomBytes = random_bytes($length * 2); // extra bytes to avoid modulo bias

        for ($i = 0; $i < $length; $i++) {
            $byte = ord($randomBytes[$i]);
            $result .= self::ALPHABET[$byte % $alphabetLen];
        }

        return $result;
    }

    private function slugExists(string $slug): bool
    {
        $row = $this->db->fetchOne(
            'SELECT id FROM notes WHERE slug = ? LIMIT 1',
            [$slug]
        );
        return $row !== false;
    }
}
