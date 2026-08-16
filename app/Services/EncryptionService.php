<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\Config;

/**
 * EncryptionService – AES-256-GCM Note Content Encryption
 *
 * Uses PHP's OpenSSL with AES-256-GCM authenticated encryption.
 * Each encryption produces a unique random IV.
 * The GCM auth tag ensures tamper detection.
 */
class EncryptionService
{
    private const CIPHER    = 'aes-256-gcm';
    private const TAG_LEN   = 16;
    private const IV_LEN    = 12; // 96-bit IV recommended for GCM

    private string $key;

    public function __construct()
    {
        $appKey = Config::env('APP_KEY', 'default_cabin_secret_key_2026_32b');

        if (str_starts_with($appKey, 'base64:')) {
            $decoded = base64_decode(substr($appKey, 7), true);
            if ($decoded !== false && strlen($decoded) === 32) {
                $this->key = $decoded;
            } else {
                $this->key = substr(hash('sha256', $appKey, true), 0, 32);
            }
        } elseif (strlen($appKey) === 32) {
            $this->key = $appKey;
        } else {
            $this->key = substr(hash('sha256', $appKey, true), 0, 32);
        }
    }

    /**
     * Encrypt plaintext content.
     *
     * @return array{encrypted: string, iv: string, tag: string}
     */
    public function encrypt(string $plaintext): array
    {
        $iv  = random_bytes(self::IV_LEN);
        $tag = '';

        $encrypted = openssl_encrypt(
            $plaintext,
            self::CIPHER,
            $this->key,
            OPENSSL_RAW_DATA,
            $iv,
            $tag,
            '',
            self::TAG_LEN
        );

        if ($encrypted === false) {
            throw new \RuntimeException('Encryption failed: ' . openssl_error_string());
        }

        return [
            'encrypted' => base64_encode($encrypted),
            'iv'        => bin2hex($iv),
            'tag'       => bin2hex($tag),
        ];
    }

    /**
     * Decrypt previously encrypted content.
     *
     * @param string $encrypted  Base64-encoded ciphertext
     * @param string $iv         Hex-encoded IV
     * @param string $tag        Hex-encoded GCM auth tag
     */
    public function decrypt(string $encrypted, string $iv, string $tag): string
    {
        $ciphertext = base64_decode($encrypted);
        $ivBytes    = hex2bin($iv);
        $tagBytes   = hex2bin($tag);

        $plaintext = openssl_decrypt(
            $ciphertext,
            self::CIPHER,
            $this->key,
            OPENSSL_RAW_DATA,
            $ivBytes,
            $tagBytes
        );

        if ($plaintext === false) {
            throw new \RuntimeException('Decryption failed. Data may be corrupted or tampered.');
        }

        return $plaintext;
    }

    /**
     * Generate a new random APP_KEY (call from setup script)
     */
    public static function generateKey(): string
    {
        return 'base64:' . base64_encode(random_bytes(32));
    }
}
