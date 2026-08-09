<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Core\Request;
use App\Core\Response;
use App\Services\CsrfService;

/**
 * CsrfMiddleware – CSRF Token Validation on POST/PUT/DELETE
 *
 * Checks for a valid CSRF token on all state-changing requests.
 * Supports both form field (_csrf_token) and header (X-CSRF-Token).
 */
class CsrfMiddleware
{
    public function handle(Request $request, Response $response, callable $next): void
    {
        if (!in_array($request->method(), ['POST', 'PUT', 'DELETE', 'PATCH'])) {
            $next();
            return;
        }

        $csrf = new CsrfService();

        // Check header first (AJAX), then form field
        $submitted = $request->header('x-csrf-token')
                  ?? $request->body('_csrf_token')
                  ?? '';

        if (!$csrf->validate((string) $submitted)) {
            if ($request->expectsJson()) {
                $response->jsonError('CSRF token invalid or expired.', 403);
                return;
            }
            $response->error(403, 'CSRF token invalid or expired.');
            return;
        }

        $next();
    }
}
