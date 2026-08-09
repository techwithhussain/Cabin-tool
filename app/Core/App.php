<?php

declare(strict_types=1);

namespace App\Core;

use App\Middleware\SecurityHeadersMiddleware;

/**
 * App – Application Bootstrap
 *
 * Initialises all services, starts the session, loads routes,
 * and dispatches the request through the middleware pipeline.
 */
class App
{
    private Router   $router;
    private Request  $request;
    private Response $response;

    public function __construct()
    {
        // 1. Load config & environment
        Config::load();

        // 2. Configure PHP error reporting
        $this->configureErrorReporting();

        // 3. Start session
        $this->startSession();

        // 4. Initialise core objects
        $this->request  = new Request();
        $this->response = new Response();
        $this->router   = new Router();

        // 5. Register routes
        $this->registerRoutes();
    }

    public function run(): void
    {
        // Apply security headers first (always)
        (new SecurityHeadersMiddleware())->handle(
            $this->request,
            $this->response,
            fn() => $this->router->dispatch($this->request, $this->response)
        );
    }

    // ─────────────────────────────────────────────
    // Session
    // ─────────────────────────────────────────────

    private function startSession(): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) return;

        $isHttps  = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443);
        $secure   = $isHttps && Config::env('SESSION_SECURE', 'true') === 'true';
        $httpOnly = Config::env('SESSION_HTTPONLY', 'true') === 'true';
        $sameSite = Config::env('SESSION_SAMESITE', 'Lax');
        $lifetime = (int) Config::env('SESSION_LIFETIME', 3600);

        session_name(Config::env('SESSION_NAME', 'cabin_session'));

        session_set_cookie_params([
            'lifetime' => $lifetime,
            'path'     => '/',
            'domain'   => '',
            'secure'   => $secure,
            'httponly' => $httpOnly,
            'samesite' => $sameSite,
        ]);

        session_start();

        // Regenerate session ID periodically to prevent fixation
        if (!isset($_SESSION['_created'])) {
            $_SESSION['_created'] = time();
        } elseif (time() - $_SESSION['_created'] > 1800) {
            session_regenerate_id(true);
            $_SESSION['_created'] = time();
        }
    }

    // ─────────────────────────────────────────────
    // Error Reporting
    // ─────────────────────────────────────────────

    private function configureErrorReporting(): void
    {
        if (APP_DEBUG) {
            error_reporting(E_ALL);
            ini_set('display_errors', '1');
        } else {
            error_reporting(0);
            ini_set('display_errors', '0');
            ini_set('log_errors', '1');
            ini_set('error_log', STORAGE_PATH . '/logs/php_errors.log');
        }

        // Set timezone
        date_default_timezone_set('UTC');

        // Upload limits
        $maxUpload = Config::env('MAX_UPLOAD_SIZE', '10485760');
        ini_set('upload_max_filesize', (string) $maxUpload);
        ini_set('post_max_size', (string) ((int)$maxUpload * 2));
    }

    // ─────────────────────────────────────────────
    // Route Registration
    // ─────────────────────────────────────────────

    private function registerRoutes(): void
    {
        require_once BASE_PATH . '/routes/web.php';

        // Pass router to the routes file via a function call
        if (function_exists('registerWebRoutes')) {
            registerWebRoutes($this->router);
        }
    }
}
