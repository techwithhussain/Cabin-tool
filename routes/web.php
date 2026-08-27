<?php

declare(strict_types=1);

use App\Core\Router;
use App\Controllers\HomeController;
use App\Controllers\NoteController;
use App\Controllers\AdminController;
use App\Controllers\CronController;
use App\Controllers\LegalController;
use App\Middleware\CsrfMiddleware;
use App\Middleware\RateLimitMiddleware;
use App\Middleware\AdminAuthMiddleware;

/**
 * Web Routes – All application routes registered here
 *
 * Middleware is applied per-route or per-group.
 * Format: $router->get('/path', [Controller::class, 'method'], [Middleware::class])
 */
function registerWebRoutes(Router $router): void
{
    // ─────────────────────────────────────────────
    // Public – Landing
    // ─────────────────────────────────────────────
    $router->get('/', [HomeController::class, 'index']);
    $router->get('/about', [HomeController::class, 'about']);

    // ─────────────────────────────────────────────
    // Legal Pages
    // ─────────────────────────────────────────────
    $router->get('/privacy', [LegalController::class, 'privacy']);
    $router->get('/terms', [LegalController::class, 'terms']);
    $router->get('/dmca', [LegalController::class, 'dmca']);
    $router->get('/disclaimer', [LegalController::class, 'disclaimer']);

    // ─────────────────────────────────────────────
    // Workspace – Create Note
    // ─────────────────────────────────────────────
    $router->get('/create', [NoteController::class, 'workspace']);

    $router->post('/note', [NoteController::class, 'create'], [
        CsrfMiddleware::class,
        // RateLimitMiddleware constructed with action name via closure workaround:
    ]);

    // ─────────────────────────────────────────────
    // Note – View
    // ─────────────────────────────────────────────
    $router->get('/note/{slug}', [NoteController::class, 'view']);

    // Password Verify
    $router->post('/note/{slug}/verify', [NoteController::class, 'verifyPassword'], [
        CsrfMiddleware::class,
    ]);

    // Update Note Content
    $router->post('/note/{slug}/update', [NoteController::class, 'update'], [
        CsrfMiddleware::class,
    ]);

    // Delete Note (owner token required)
    $router->post('/note/{slug}/delete', [NoteController::class, 'delete'], [
        CsrfMiddleware::class,
    ]);

    // ─────────────────────────────────────────────
    // Cron – Cleanup (key-protected, no CSRF needed)
    // ─────────────────────────────────────────────
    $router->get('/cron/cleanup', [CronController::class, 'cleanup']);

    // ─────────────────────────────────────────────
    // Admin Panel
    // ─────────────────────────────────────────────

    // Login (no auth required)
    $router->get('/admin/login', [AdminController::class, 'loginForm']);
    $router->post('/admin/login', [AdminController::class, 'login'], [CsrfMiddleware::class]);

    // Authenticated admin routes
    $router->group([AdminAuthMiddleware::class], function (Router $router) {
        $router->get('/admin/dashboard', [AdminController::class, 'dashboard']);
        $router->get('/admin/notes',     [AdminController::class, 'notes']);
        $router->get('/admin/logs',      [AdminController::class, 'logs']);
        $router->post('/admin/logout',   [AdminController::class, 'logout'], [CsrfMiddleware::class]);
        $router->post('/admin/note/{slug}/delete', [AdminController::class, 'deleteNote'], [CsrfMiddleware::class]);
    });

    // ─────────────────────────────────────────────
    // Direct Custom Slug Routing (e.g. cabinn.in/hello)
    // If note exists -> show note (or password lock)
    // If note does not exist -> open editor with slug pre-filled!
    // ─────────────────────────────────────────────
    $router->get('/{slug}', [NoteController::class, 'handleDirectSlug']);
}
