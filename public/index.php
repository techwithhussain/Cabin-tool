<?php

/**
 * Cabin – Secure Notes & Private Sharing Platform
 * Public Front Controller
 *
 * All HTTP requests are routed through this single entry point.
 * @author Tech With Hussain
 */

declare(strict_types=1);

// ─────────────────────────────────────────────
// 1. Define base paths
// ─────────────────────────────────────────────
define('BASE_PATH', dirname(__DIR__));
define('APP_PATH',  BASE_PATH . '/app');
define('PUBLIC_PATH', __DIR__);
define('STORAGE_PATH', BASE_PATH . '/storage');
define('CONFIG_PATH', BASE_PATH . '/config');

// ─────────────────────────────────────────────
// 2. PHP version guard
// ─────────────────────────────────────────────
if (PHP_VERSION_ID < 80300) {
    http_response_code(500);
    die('Cabin requires PHP 8.3 or higher. Current: ' . PHP_VERSION);
}

// ─────────────────────────────────────────────
// 3. Autoloader (Composer PSR-4 with native fallback)
// ─────────────────────────────────────────────
$autoloader = BASE_PATH . '/vendor/autoload.php';
if (file_exists($autoloader)) {
    require_once $autoloader;
} else {
    // Native PSR-4 autoloader fallback for App\ namespace
    spl_autoload_register(function (string $class): void {
        $prefix = 'App\\';
        if (str_starts_with($class, $prefix)) {
            $relativeClass = substr($class, strlen($prefix));
            $file = APP_PATH . '/' . str_replace('\\', '/', $relativeClass) . '.php';
            if (file_exists($file)) {
                require_once $file;
            }
        }
    });
}

// ─────────────────────────────────────────────
// 4. Bootstrap & Run Application
// ─────────────────────────────────────────────
use App\Core\App;

try {
    $app = new App();
    $app->run();
} catch (\Throwable $e) {
    // Log exception details to storage/logs/app.log
    $logMsg = sprintf("[%s] %s: %s in %s:%d\nStack trace:\n%s\n\n",
        date('Y-m-d H:i:s'),
        get_class($e),
        $e->getMessage(),
        $e->getFile(),
        $e->getLine(),
        $e->getTraceAsString()
    );
    @file_put_contents(STORAGE_PATH . '/logs/app.log', $logMsg, FILE_APPEND);

    // Last-resort error handler
    if (defined('APP_DEBUG') && APP_DEBUG) {
        http_response_code(500);
        echo '<div style="font-family:sans-serif;padding:30px;background:#fef2f2;border:1px solid #fecaca;border-radius:12px;margin:30px;color:#991b1b;">';
        echo '<h2 style="margin-top:0;">⚠️ Development Error Debugger</h2>';
        echo '<p><strong>' . get_class($e) . ':</strong> ' . htmlspecialchars($e->getMessage()) . '</p>';
        echo '<p><em>File: ' . htmlspecialchars($e->getFile()) . ' (Line ' . $e->getLine() . ')</em></p>';
        echo '<pre style="background:#fff;padding:15px;border-radius:8px;overflow-x:auto;font-size:13px;">' . htmlspecialchars($e->getTraceAsString()) . '</pre>';
        echo '</div>';
    } else {
        http_response_code(500);
        $errorView = APP_PATH . '/Views/errors/500.php';
        if (file_exists($errorView)) {
            include $errorView;
        } else {
            echo '<h1>500 – Internal Server Error</h1><p>Something went wrong. Please try again later.</p>';
        }
    }
}
