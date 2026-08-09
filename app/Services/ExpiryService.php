<?php

declare(strict_types=1);

namespace App\Services;

/**
 * ExpiryService – Note Expiry Time Management
 *
 * Converts human-readable expiry options into a UTC DateTime.
 * Validates submitted expiry values.
 */
class ExpiryService
{
    /** Map of option value => label for UI */
    public const OPTIONS = [
        'never' => 'Never',
        '1h'    => '1 Hour',
        '6h'    => '6 Hours',
        '12h'   => '12 Hours',
        '24h'   => '24 Hours',
        '3d'    => '3 Days',
        '7d'    => '7 Days',
        '30d'   => '30 Days',
    ];

    /** Map of option value => seconds offset */
    private const SECONDS = [
        '1h'  => 3600,
        '6h'  => 21600,
        '12h' => 43200,
        '24h' => 86400,
        '3d'  => 259200,
        '7d'  => 604800,
        '30d' => 2592000,
    ];

    /**
     * Convert a user-submitted option to a UTC DateTime string (for DB storage)
     * Returns null if 'never' or invalid option
     */
    public function toDateTime(string $option): ?string
    {
        if ($option === 'never' || !isset(self::SECONDS[$option])) {
            return null;
        }

        $seconds = self::SECONDS[$option];
        return date('Y-m-d H:i:s', time() + $seconds);
    }

    /**
     * Validate that a submitted expiry option is one of the allowed values
     */
    public function isValid(string $option): bool
    {
        return array_key_exists($option, self::OPTIONS);
    }

    /**
     * Get the default expiry option key
     */
    public function default(): string
    {
        return '24h';
    }

    /**
     * Check if a note (by its expires_at string) is currently expired
     */
    public function isExpired(?string $expiresAt): bool
    {
        if ($expiresAt === null) return false;

        $expiry = new \DateTimeImmutable($expiresAt, new \DateTimeZone('UTC'));
        $now    = new \DateTimeImmutable('now', new \DateTimeZone('UTC'));

        return $now >= $expiry;
    }

    /**
     * Get remaining seconds until expiry (for countdown timer)
     */
    public function remainingSeconds(?string $expiresAt): int
    {
        if ($expiresAt === null) return -1;

        $expiry = new \DateTimeImmutable($expiresAt, new \DateTimeZone('UTC'));
        $now    = new \DateTimeImmutable('now', new \DateTimeZone('UTC'));

        return max(0, $expiry->getTimestamp() - $now->getTimestamp());
    }

    /**
     * Format remaining time as human-readable string
     */
    public function humanRemaining(?string $expiresAt): string
    {
        if ($expiresAt === null) return 'Never';

        $seconds = $this->remainingSeconds($expiresAt);

        if ($seconds <= 0)      return 'Expired';
        if ($seconds < 60)      return $seconds . 's';
        if ($seconds < 3600)    return floor($seconds / 60) . 'm ' . ($seconds % 60) . 's';
        if ($seconds < 86400)   return floor($seconds / 3600) . 'h ' . floor(($seconds % 3600) / 60) . 'm';

        return floor($seconds / 86400) . 'd ' . floor(($seconds % 86400) / 3600) . 'h';
    }
}
