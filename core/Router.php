<?php

declare(strict_types=1);

namespace Core;

/**
 * HTTP router with middleware pipeline support.
 */
final class Router
{
    /** @var array<int, array<string, mixed>> */
    private array $routes = [];

    /** @var array<int, class-string> */
    private array $globalMiddleware = [];

    public function get(string $uri, callable|array $action, array $middleware = []): self
    {
        return $this->addRoute('GET', $uri, $action, $middleware);
    }

    public function post(string $uri, callable|array $action, array $middleware = []): self
    {
        return $this->addRoute('POST', $uri, $action, $middleware);
    }

    public function put(string $uri, callable|array $action, array $middleware = []): self
    {
        return $this->addRoute('PUT', $uri, $action, $middleware);
    }

    public function delete(string $uri, callable|array $action, array $middleware = []): self
    {
        return $this->addRoute('DELETE', $uri, $action, $middleware);
    }

    /**
     * @param array<int, class-string> $middleware
     */
    public function group(array $options, callable $callback): void
    {
        $prefix = rtrim((string) ($options['prefix'] ?? ''), '/');
        $middleware = $options['middleware'] ?? [];
        $previousPrefix = $this->currentPrefix ?? '';
        $previousMiddleware = $this->globalMiddleware;

        $this->currentPrefix = $previousPrefix . $prefix;
        $this->globalMiddleware = array_merge($previousMiddleware, $middleware);

        $callback($this);

        $this->currentPrefix = $previousPrefix;
        $this->globalMiddleware = $previousMiddleware;
    }

    private string $currentPrefix = '';

    private function addRoute(string $method, string $uri, callable|array $action, array $middleware): self
    {
        $normalizedUri = $this->normalizeUri(($this->currentPrefix ?? '') . $uri);

        $this->routes[] = [
            'method' => strtoupper($method),
            'uri' => $normalizedUri,
            'pattern' => $this->convertUriToRegex($normalizedUri),
            'action' => $action,
            'middleware' => array_merge($this->globalMiddleware, $middleware),
            'params' => [],
        ];

        return $this;
    }

    public function dispatch(Request $request): void
    {
        $method = $request->method();
        $uri = $this->normalizeUri($request->uri());

        foreach ($this->routes as $route) {
            if ($route['method'] !== $method) {
                continue;
            }

            if (!preg_match($route['pattern'], $uri, $matches)) {
                continue;
            }

            $params = [];
            foreach ($matches as $key => $value) {
                if (is_string($key)) {
                    $params[$key] = $value;
                }
            }

            $this->runPipeline($route['middleware'], $request, function () use ($route, $params) {
                $this->invokeAction($route['action'], $params);
            });

            return;
        }

        http_response_code(404);
        View::render('errors/404', [
            'title' => 'Page Not Found',
            'message' => 'The page you requested does not exist.',
        ], 'layouts/main');
    }

    /**
     * @param array<int, class-string> $middleware
     */
    private function runPipeline(array $middleware, Request $request, callable $destination): void
    {
        $runner = array_reduce(
            array_reverse($middleware),
            static function (callable $next, string $middlewareClass) use ($request) {
                return static function () use ($middlewareClass, $request, $next): void {
                    $instance = new $middlewareClass();

                    if (!method_exists($instance, 'handle')) {
                        throw new \RuntimeException("Middleware {$middlewareClass} must implement handle().");
                    }

                    $instance->handle($request, $next);
                };
            },
            $destination
        );

        $runner();
    }

    private function invokeAction(callable|array $action, array $params): void
    {
        if (is_callable($action)) {
            call_user_func_array($action, $params);
            return;
        }

        [$controllerClass, $method] = $action;

        if (!class_exists($controllerClass)) {
            throw new \RuntimeException("Controller {$controllerClass} not found.");
        }

        $controller = new $controllerClass();

        if (!method_exists($controller, $method)) {
            throw new \RuntimeException("Method {$method} not found on {$controllerClass}.");
        }

        call_user_func_array([$controller, $method], $params);
    }

    private function normalizeUri(string $uri): string
    {
        $uri = parse_url($uri, PHP_URL_PATH) ?: '/';
        $uri = '/' . trim($uri, '/');

        return $uri === '/' ? '/' : rtrim($uri, '/');
    }

    private function convertUriToRegex(string $uri): string
    {
        $pattern = preg_replace('#\{([a-zA-Z_][a-zA-Z0-9_]*)\}#', '(?P<$1>[^/]+)', $uri);

        return '#^' . $pattern . '$#';
    }
}
