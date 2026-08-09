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
        $appKey = Config::env('APP_KEY', '');

        if (str_starts_with($appKey, 'base64:')) {
            $this->key = base64_decode(substr($appKey, 7));
        } else {
            $this->key = $appKey;
        }

        if (strlen($this->key) !== 32) {
            throw new \RuntimeException(
                'APP_KEY must be exactly 32 bytes. Generate with: php -r "echo \'base64:\' . base64_encode(random_bytes(32)) . PHP_EOL;"'
            );
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
