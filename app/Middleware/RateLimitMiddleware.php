<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Core\Request;
use App\Core\Response;
use App\Services\RateLimitService;

/**
 * RateLimitMiddleware – IP-based Rate Limiting
 *
 * Used on note creation and password verification routes.
 * Delegates to RateLimitService for DB-backed tracking.
 */
class RateLimitMiddleware
{
    private string $action;

    public function __construct(string $action = 'view')
    {
        $this->action = $action;
    }

    public function handle(Request $request, Response $response, callable $next): void
    {
        $rateLimiter = new RateLimitService();
        $ipHash      = $request->ipHash();

        if (!$rateLimiter->check($ipHash, $this->action)) {
            $retryAfter = $rateLimiter->getRetryAfter($ipHash, $this->action);

            header("Retry-After: $retryAfter");

            if ($request->expectsJson()) {
                $response->jsonError(
                    'Too many requests. Please try again later.',
                    429,
                    ['retry_after' => $retryAfter]
                );
                return;
            }

            $response->error(429, 'Too many requests. Please try again in ' . ceil($retryAfter / 60) . ' minutes.');
            return;
        }

        $next();
    }
}
