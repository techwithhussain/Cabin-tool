<?php

declare(strict_types=1);

namespace App\Core;

/**
 * Request – HTTP Request Abstraction
 *
 * Wraps all superglobals into a clean, immutable-feeling API.
 */
class Request
{
    private string $method;
    private string $path;
    private array  $query;
    private array  $body;
    private array  $files;
    private array  $headers;
    private array  $params = []; // Route parameters (set by Router)

    public function __construct()
    {
        $this->method  = strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');
        $this->path    = $this->parsePath();
        $this->query   = $_GET ?? [];
        $this->body    = $this->parseBody();
        $this->files   = $_FILES ?? [];
        $this->headers = $this->parseHeaders();
    }

    // ─────────────────────────────────────────────
    // Path & Method
    // ─────────────────────────────────────────────

    private function parsePath(): string
    {
        $uri = $_SERVER['REQUEST_URI'] ?? '/';
        $path = parse_url($uri, PHP_URL_PATH);
        return '/' . trim($path ?? '/', '/');
    }

    private function parseBody(): array
    {
        if ($this->isJson()) {
            $json = file_get_contents('php://input');
            return json_decode($json ?: '{}', true) ?? [];
        }
        return $_POST ?? [];
    }

    private function parseHeaders(): array
    {
        if (function_exists('getallheaders')) {
            return array_change_key_case(getallheaders() ?: [], CASE_LOWER);
        }
        $headers = [];
        foreach ($_SERVER as $key => $value) {
            if (str_starts_with($key, 'HTTP_')) {
                $header = str_replace('_', '-', strtolower(substr($key, 5)));
                $headers[$header] = $value;
            }
        }
        return $headers;
    }

    // ─────────────────────────────────────────────
    // Accessors
    // ─────────────────────────────────────────────

    public function method(): string      { return $this->method; }
    public function path(): string        { return $this->path; }
    public function params(): array       { return $this->params; }
    public function setParams(array $p): void { $this->params = $p; }

    public function param(string $key, mixed $default = null): mixed
    {
        return $this->params[$key] ?? $default;
    }

    public function query(string $key = null, mixed $default = null): mixed
    {
        if ($key === null) return $this->query;
        return $this->query[$key] ?? $default;
    }

    public function body(string $key = null, mixed $default = null): mixed
    {
        if ($key === null) return $this->body;
        return $this->body[$key] ?? $default;
    }

    public function file(string $key): array|null
    {
        return $this->files[$key] ?? null;
    }

    public function files(): array { return $this->files; }

    public function header(string $key, mixed $default = null): mixed
    {
        return $this->headers[strtolower($key)] ?? $default;
    }

    // ─────────────────────────────────────────────
    // Type Checks
    // ─────────────────────────────────────────────

    public function isGet(): bool    { return $this->method === 'GET'; }
    public function isPost(): bool   { return $this->method === 'POST'; }
    public function isDelete(): bool { return $this->method === 'DELETE'; }
    public function isPut(): bool    { return $this->method === 'PUT'; }
    public function isPatch(): bool  { return $this->method === 'PATCH'; }

    public function isJson(): bool
    {
        $ct = $this->header('content-type', '');
        return str_contains($ct, 'application/json');
    }

    public function isAjax(): bool
    {
        return $this->header('x-requested-with') === 'XMLHttpRequest';
    }

    public function expectsJson(): bool
    {
        return $this->isAjax() || $this->isJson() ||
               str_contains($this->header('accept', ''), 'application/json');
    }

    // ─────────────────────────────────────────────
    // IP Address
    // ─────────────────────────────────────────────

    public function ip(): string
    {
        // Check common proxy headers (validate format)
        $candidates = [
            $_SERVER['HTTP_CF_CONNECTING_IP'] ?? '',  // Cloudflare
            $_SERVER['HTTP_X_FORWARDED_FOR']  ?? '',
            $_SERVER['HTTP_X_REAL_IP']         ?? '',
            $_SERVER['REMOTE_ADDR']            ?? '',
        ];

        foreach ($candidates as $ip) {
            $ip = trim(explode(',', $ip)[0]);
            if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
                return $ip;
            }
        }

        // Fallback (may be private IP in local dev)
        return trim(explode(',', $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1')[0]);
    }

    /** Returns a SHA-256 hash of the IP (for privacy-safe storage) */
    public function ipHash(): string
    {
        return hash('sha256', $this->ip() . Config::env('APP_KEY', 'default_salt'));
    }

    public function userAgent(): string
    {
        return substr($_SERVER['HTTP_USER_AGENT'] ?? '', 0, 512);
    }
}
