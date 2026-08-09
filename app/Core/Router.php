<?php

declare(strict_types=1);

namespace App\Core;

/**
 * Router – Pattern-based HTTP Router
 *
 * Supports GET, POST, DELETE, PUT methods.
 * Routes can have middleware groups applied.
 * Parameters extracted via regex named groups.
 */
class Router
{
    private array $routes     = [];
    private array $middleware = [];
    private array $groupMiddleware = [];

    // ─────────────────────────────────────────────
    // Route Registration
    // ─────────────────────────────────────────────

    public function get(string $pattern, array|callable $handler, array $middleware = []): void
    {
        $this->add('GET', $pattern, $handler, $middleware);
    }

    public function post(string $pattern, array|callable $handler, array $middleware = []): void
    {
        $this->add('POST', $pattern, $handler, $middleware);
    }

    public function delete(string $pattern, array|callable $handler, array $middleware = []): void
    {
        $this->add('DELETE', $pattern, $handler, $middleware);
    }

    public function put(string $pattern, array|callable $handler, array $middleware = []): void
    {
        $this->add('PUT', $pattern, $handler, $middleware);
    }

    private function add(string $method, string $pattern, array|callable $handler, array $middleware): void
    {
        $this->routes[] = [
            'method'     => $method,
            'pattern'    => $this->compilePattern($pattern),
            'raw'        => $pattern,
            'handler'    => $handler,
            'middleware' => array_merge($this->groupMiddleware, $middleware),
        ];
    }

    /** Convert /note/{slug} to a named regex */
    private function compilePattern(string $pattern): string
    {
        $pattern = preg_replace('/\{([a-zA-Z_][a-zA-Z0-9_]*)\}/', '(?P<$1>[^/]+)', $pattern);
        return '@^' . $pattern . '$@';
    }

    // ─────────────────────────────────────────────
    // Middleware Groups
    // ─────────────────────────────────────────────

    public function group(array $middleware, callable $callback): void
    {
        $prev = $this->groupMiddleware;
        $this->groupMiddleware = array_merge($prev, $middleware);
        $callback($this);
        $this->groupMiddleware = $prev;
    }

    // ─────────────────────────────────────────────
    // Dispatch
    // ─────────────────────────────────────────────

    public function dispatch(Request $request, Response $response): void
    {
        $method = $request->method();
        $path   = $request->path();

        // Support _method override in forms (POST + _method=DELETE)
        if ($method === 'POST' && isset($_POST['_method'])) {
            $override = strtoupper($_POST['_method']);
            if (in_array($override, ['DELETE', 'PUT', 'PATCH'])) {
                $method = $override;
            }
        }

        foreach ($this->routes as $route) {
            if ($route['method'] !== $method) continue;

            if (preg_match($route['pattern'], $path, $matches)) {
                // Extract named params
                $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
                $request->setParams($params);

                // Run middleware chain
                $this->runMiddleware($route['middleware'], $request, $response, function () use ($route, $request, $response) {
                    $this->callHandler($route['handler'], $request, $response);
                });

                return;
            }
        }

        // 404
        $response->error(404, 'Page not found');
    }

    private function runMiddleware(array $middleware, Request $request, Response $response, callable $next): void
    {
        if (empty($middleware)) {
            $next();
            return;
        }

        $middlewareClass = array_shift($middleware);

        /** @var \App\Middleware\MiddlewareInterface $mw */
        $mw = new $middlewareClass();
        $mw->handle($request, $response, function () use ($middleware, $request, $response, $next) {
            $this->runMiddleware($middleware, $request, $response, $next);
        });
    }

    private function callHandler(array|callable $handler, Request $request, Response $response): void
    {
        if (is_callable($handler)) {
            $handler($request, $response);
            return;
        }

        // [$ControllerClass, 'method']
        [$class, $method] = $handler;

        if (!class_exists($class)) {
            throw new \RuntimeException("Controller not found: $class");
        }

        $controller = new $class();
        if (!method_exists($controller, $method)) {
            throw new \RuntimeException("Method $method not found in $class");
        }

        $controller->$method($request, $response);
    }
}
