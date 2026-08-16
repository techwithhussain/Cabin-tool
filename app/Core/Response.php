<?php

declare(strict_types=1);

namespace App\Core;

/**
 * Response – HTTP Response Builder
 *
 * Handles rendering views, sending JSON, redirecting, and error pages.
 */
class Response
{
    private int    $statusCode = 200;
    private array  $headers    = [];

    // ─────────────────────────────────────────────
    // Status
    // ─────────────────────────────────────────────

    public function status(int $code): static
    {
        $this->statusCode = $code;
        return $this;
    }

    public function header(string $name, string $value): static
    {
        $this->headers[$name] = $value;
        return $this;
    }

    private function sendHeaders(): void
    {
        http_response_code($this->statusCode);
        foreach ($this->headers as $name => $value) {
            header("$name: $value");
        }
    }

    // ─────────────────────────────────────────────
    // View Rendering
    // ─────────────────────────────────────────────

    /**
     * Render a view file with optional data
     *
     * @param string $view  Dot-notation: 'landing.index' or 'note.view'
     * @param array  $data  Variables made available in the view
     */
    public function view(string $view, array $data = [], string $layout = 'main'): void
    {
        $this->sendHeaders();

        // Convert dot notation to path
        $viewPath = APP_PATH . '/Views/' . str_replace('.', '/', $view) . '.php';

        if (!file_exists($viewPath)) {
            throw new \RuntimeException("View not found: $view (looked in $viewPath)");
        }

        // Extract data into local scope
        extract($data, EXTR_SKIP);

        // If layout is 'none', render view directly
        if ($layout === 'none') {
            require $viewPath;
            return;
        }

        $layoutPath = APP_PATH . '/Views/layouts/' . $layout . '.php';
        if (!file_exists($layoutPath)) {
            require $viewPath;
            return;
        }

        // Buffer the view content
        ob_start();
        require $viewPath;
        $content = ob_get_clean();

        // Render the layout with $content available
        // Enable output compression for faster delivery
        if (!headers_sent() && str_contains($_SERVER['HTTP_ACCEPT_ENCODING'] ?? '', 'gzip')) {
            header('Content-Encoding: gzip');
            ob_start('ob_gzhandler');
            require $layoutPath;
            ob_end_flush();
        } else {
            require $layoutPath;
        }
    }

    /**
     * Render an error view
     */
    public function error(int $code, string $message = ''): void
    {
        $this->statusCode = $code;
        $this->sendHeaders();

        $viewPath = APP_PATH . "/Views/errors/$code.php";
        if (!file_exists($viewPath)) {
            echo "<h1>$code Error</h1><p>" . htmlspecialchars($message) . "</p>";
            return;
        }

        require $viewPath;
    }

    // ─────────────────────────────────────────────
    // JSON
    // ─────────────────────────────────────────────

    public function json(mixed $data, int $status = 200): void
    {
        $this->status($status)->header('Content-Type', 'application/json');
        $this->sendHeaders();
        echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        exit;
    }

    public function jsonSuccess(mixed $data = null, string $message = 'Success', int $status = 200): void
    {
        $this->json([
            'success' => true,
            'message' => $message,
            'data'    => $data,
        ], $status);
    }

    public function jsonError(string $message, int $status = 400, array $errors = []): void
    {
        $payload = ['success' => false, 'message' => $message];
        if (!empty($errors)) $payload['errors'] = $errors;
        $this->json($payload, $status);
    }

    // ─────────────────────────────────────────────
    // Redirects
    // ─────────────────────────────────────────────

    public function redirect(string $url, int $code = 302): never
    {
        http_response_code($code);
        header('Location: ' . $url);
        exit;
    }

    public function back(): never
    {
        $referer = $_SERVER['HTTP_REFERER'] ?? '/';
        $this->redirect($referer);
    }

    // ─────────────────────────────────────────────
    // Raw Output
    // ─────────────────────────────────────────────

    public function send(string $content, string $contentType = 'text/html'): void
    {
        $this->header('Content-Type', $contentType);
        $this->sendHeaders();
        echo $content;
        exit;
    }

    public function download(string $filePath, string $filename): void
    {
        if (!file_exists($filePath)) {
            $this->error(404, 'File not found');
            return;
        }

        $this->sendHeaders();
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . addslashes($filename) . '"');
        header('Content-Length: ' . filesize($filePath));
        header('Cache-Control: no-cache');
        readfile($filePath);
        exit;
    }
}
