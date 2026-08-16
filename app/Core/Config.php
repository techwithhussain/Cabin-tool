<?php

declare(strict_types=1);

namespace App\Core;

/**
 * Config – .env parser and config registry
 *
 * Parses the root .env file and loads config files from /config/.
 */
class Config
{
    private static array $env    = [];
    private static array $config = [];
    private static bool  $loaded = false;

    /** Boot the config system */
    public static function load(): void
    {
        if (self::$loaded) return;

        self::loadEnv(BASE_PATH . '/.env');
        self::loadPhpConfigs();
        self::$loaded = true;

        // Propagate debug flag
        define('APP_DEBUG', self::env('APP_DEBUG', false) === 'true' || self::env('APP_DEBUG', false) === true);
    }

    // ─────────────────────────────────────────────
    // .env Parser
    // ─────────────────────────────────────────────

    private static function loadEnv(string $path): void
    {
        if (!file_exists($path)) {
            // In cloud environments like Vercel, env variables come directly from getenv() / $_ENV
            return;
        }

        $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        foreach ($lines as $line) {
            $line = trim($line);

            // Skip comments
            if (str_starts_with($line, '#') || !str_contains($line, '=')) continue;

            [$key, $value] = explode('=', $line, 2);
            $key   = trim($key);
            $value = trim($value);

            // Strip inline comments
            if (str_contains($value, ' #')) {
                $value = trim(explode(' #', $value, 2)[0]);
            }

            // Strip surrounding quotes
            if (
                (str_starts_with($value, '"') && str_ends_with($value, '"')) ||
                (str_starts_with($value, "'") && str_ends_with($value, "'"))
            ) {
                $value = substr($value, 1, -1);
            }

            self::$env[$key] = $value;
            $_ENV[$key]      = $value;
            putenv("$key=$value");
        }
    }

    private static function loadPhpConfigs(): void
    {
        $configFiles = ['app', 'database', 'security'];

        foreach ($configFiles as $file) {
            $path = CONFIG_PATH . "/$file.php";
            if (file_exists($path)) {
                self::$config[$file] = require $path;
            }
        }
    }

    // ─────────────────────────────────────────────
    // Accessors
    // ─────────────────────────────────────────────

    /** Get a .env value */
    public static function env(string $key, mixed $default = null): mixed
    {
        return self::$env[$key] ?? $_ENV[$key] ?? getenv($key) ?: $default;
    }

    /** Get a config value using dot notation: config('database.host') */
    public static function get(string $key, mixed $default = null): mixed
    {
        $parts = explode('.', $key, 2);
        $file  = $parts[0];
        $subKey = $parts[1] ?? null;

        if (!isset(self::$config[$file])) return $default;

        if ($subKey === null) return self::$config[$file];

        return self::$config[$file][$subKey] ?? $default;
    }

    /** Set a runtime config value */
    public static function set(string $key, mixed $value): void
    {
        $parts = explode('.', $key, 2);
        if (count($parts) === 2) {
            self::$config[$parts[0]][$parts[1]] = $value;
        } else {
            self::$config[$key] = $value;
        }
    }
}
