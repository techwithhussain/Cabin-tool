<?php

declare(strict_types=1);

namespace App\Services;

/**
 * CsrfService – CSRF Token Management
 *
 * Generates, stores, and validates CSRF tokens in the PHP session.
 * Uses a double-submit cookie pattern backed by session storage.
 */
class CsrfService
{
    private const TOKEN_KEY    = '_cabin_csrf_token';
    private const TOKEN_LENGTH = 32;

    /**
     * Get the current CSRF token, generating one if it doesn't exist
     */
    public function getToken(): string
    {
        if (empty($_SESSION[self::TOKEN_KEY])) {
            $_SESSION[self::TOKEN_KEY] = bin2hex(random_bytes(self::TOKEN_LENGTH));
        }

        return $_SESSION[self::TOKEN_KEY];
    }

    /**
     * Validate a submitted CSRF token (timing-attack safe)
     */
    public function validate(string $submittedToken): bool
    {
        $sessionToken = $_SESSION[self::TOKEN_KEY] ?? '';

        if (empty($sessionToken) || empty($submittedToken)) {
            return false;
        }

        return hash_equals($sessionToken, $submittedToken);
    }

    /**
     * Rotate (regenerate) the CSRF token
     */
    public function rotate(): void
    {
        $_SESSION[self::TOKEN_KEY] = bin2hex(random_bytes(self::TOKEN_LENGTH));
    }

    /**
     * Get the HTML hidden input field for forms
     */
    public function field(): string
    {
        $token = $this->getToken();
        return '<input type="hidden" name="_csrf_token" value="' . htmlspecialchars($token, ENT_QUOTES, 'UTF-8') . '">';
    }

    /**
     * Get the token for use in AJAX headers (X-CSRF-Token)
     */
    public function meta(): string
    {
        return '<meta name="csrf-token" content="' . htmlspecialchars($this->getToken(), ENT_QUOTES, 'UTF-8') . '">';
    }
}
