<?php

declare(strict_types=1);

namespace App\Models;

/**
 * Note – Note Data Transfer Object
 *
 * Represents a single note record from the database.
 * Immutable value object hydrated from DB rows.
 */
class Note
{
    public function __construct(
        public readonly int     $id,
        public readonly string  $slug,
        public readonly string  $contentEncrypted,
        public readonly string  $contentIv,
        public readonly string  $contentTag,
        public readonly ?string $passwordHash,
        public readonly ?string $expiresAt,
        public readonly bool    $isExpired,
        public readonly bool    $burnAfterRead,
        public readonly int     $viewCount,
        public readonly string  $creatorIpHash,
        public readonly string  $ownerTokenHash,
        public readonly string  $createdAt,
        public readonly string  $updatedAt,
        public readonly ?string $deletedAt,
    ) {}

    public function isPasswordProtected(): bool
    {
        return $this->passwordHash !== null;
    }

    public function isDeleted(): bool
    {
        return $this->deletedAt !== null;
    }

    public function hasExpiry(): bool
    {
        return $this->expiresAt !== null;
    }

    /** Hydrate from a DB row array */
    public static function fromRow(array $row): self
    {
        return new self(
            id:               (int) $row['id'],
            slug:             $row['slug'],
            contentEncrypted: $row['content_encrypted'],
            contentIv:        $row['content_iv'],
            contentTag:       $row['content_tag'],
            passwordHash:     $row['password_hash'],
            expiresAt:        $row['expires_at'],
            isExpired:        (bool) $row['is_expired'],
            burnAfterRead:    (bool) $row['burn_after_read'],
            viewCount:        (int) $row['view_count'],
            creatorIpHash:    $row['creator_ip_hash'],
            ownerTokenHash:   $row['owner_token_hash'],
            createdAt:        $row['created_at'],
            updatedAt:        $row['updated_at'],
            deletedAt:        $row['deleted_at'],
        );
    }
}
