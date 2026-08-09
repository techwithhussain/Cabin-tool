<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Core\Request;
use App\Core\Response;
use App\Core\Config;

/**
 * SecurityHeadersMiddleware – OWASP Security Headers
 *
 * Applied to ALL requests. Sets CSP, HSTS, X-Frame-Options, etc.
 */
class SecurityHeadersMiddleware
{
    public function handle(Request $request, Response $response, callable $next): void
    {
        $appUrl = Config::env('APP_URL', 'https://localhost');
        $isHttps = str_starts_with($appUrl, 'https://');

        // Content Security Policy
        $csp = implode('; ', [
            "default-src 'self'",
            "script-src 'self' 'unsafe-inline' https://fonts.googleapis.com",
            "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com https://fonts.gstatic.com",
            "font-src 'self' https://fonts.gstatic.com data:",
            "img-src 'self' data: blob:",
            "connect-src 'self'",
            "frame-ancestors 'none'",
            "base-uri 'self'",
            "form-action 'self'",
        ]);

        header("Content-Security-Policy: $csp");
        header('X-Content-Type-Options: nosniff');
        header('X-Frame-Options: DENY');
        header('X-XSS-Protection: 1; mode=block');
        header('Referrer-Policy: strict-origin-when-cross-origin');
        header('Permissions-Policy: camera=(), microphone=(), geolocation=(), payment=()');

        if ($isHttps) {
            header('Strict-Transport-Security: max-age=31536000; includeSubDomains; preload');
        }

        // Remove revealing headers
        header_remove('X-Powered-By');
        header_remove('Server');

        $next();
    }
}
