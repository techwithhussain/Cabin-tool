<?php

declare(strict_types=1);

namespace App\Services;

/**
 * PasswordService – Argon2id Password Hashing
 *
 * Uses PHP's password_hash with PASSWORD_ARGON2ID (most secure available).
 * Automatically re-hashes if cost factors are upgraded.
 */
class PasswordService
{
    /** Argon2id tuning – balance security vs. performance on shared hosting */
    private const OPTIONS = [
        'memory_cost' => 65536,  // 64 MB
        'time_cost'   => 4,      // 4 iterations
        'threads'     => 2,
    ];

    /**
     * Hash a plain-text password using Argon2id
     */
    public function hash(string $password): string
    {
        if (!defined('PASSWORD_ARGON2ID')) {
            throw new \RuntimeException('PASSWORD_ARGON2ID requires PHP 7.3+ with Argon2 support.');
        }

        $hash = password_hash($password, PASSWORD_ARGON2ID, self::OPTIONS);

        if ($hash === false) {
            throw new \RuntimeException('Password hashing failed.');
        }

        return $hash;
    }

    /**
     * Verify a password against its hash (timing-attack safe)
     *
     * @return bool True if password is correct
     */
    public function verify(string $password, string $hash): bool
    {
        return password_verify($password, $hash);
    }

    /**
     * Check if a hash needs to be re-hashed (algorithm/cost factor upgrade)
     */
    public function needsRehash(string $hash): bool
    {
        return password_needs_rehash($hash, PASSWORD_ARGON2ID, self::OPTIONS);
    }

    /**
     * Validate password before hashing
     * Notes can have passwords 1-128 characters long
     */
    public function validate(string $password): bool
    {
        return strlen($password) >= 1 && strlen($password) <= 128;
    }
}
