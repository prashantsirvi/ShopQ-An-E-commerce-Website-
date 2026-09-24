<?php

declare(strict_types=1);

namespace Core;

/**
 * Application kernel — owns the router and orchestrates request lifecycle.
 */
final class App
{
    private Router $router;
    private array $config;

    public function __construct(array $config)
    {
        $this->config = $config;
        $this->router = new Router();
    }

    public function router(): Router
    {
        return $this->router;
    }

    public function config(string $key = null, mixed $default = null): mixed
    {
        if ($key === null) {
            return $this->config;
        }

        $segments = explode('.', $key);
        $value = $this->config;

        foreach ($segments as $segment) {
            if (!is_array($value) || !array_key_exists($segment, $value)) {
                return $default;
            }

            $value = $value[$segment];
        }

        return $value;
    }

    public function run(): void
    {
        try {
            $app = $this;
            require CONFIG_PATH . '/routes.php';
            $this->router->dispatch(Request::capture());
        } catch (\Throwable $exception) {
            $this->handleException($exception);
        }
    }

    private function handleException(\Throwable $exception): void
    {
        $debug = (bool) $this->config('debug', false);

        if ($debug) {
            http_response_code(500);
            echo '<pre style="font-family:monospace;padding:16px;background:#1a1a2e;color:#fff;">';
            echo htmlspecialchars($exception->getMessage(), ENT_QUOTES, 'UTF-8') . "\n\n";
            echo htmlspecialchars($exception->getTraceAsString(), ENT_QUOTES, 'UTF-8');
            echo '</pre>';
            return;
        }

        http_response_code(500);
        View::render('errors/500', [
            'title' => 'Server Error',
            'message' => 'Something went wrong. Please try again later.',
        ], 'layouts/main');
    }
}
