<?php

declare(strict_types=1);

namespace Core;

/**
 * Base controller — shared view rendering and flash messaging.
 */
abstract class Controller
{
    protected function view(string $view, array $data = [], ?string $layout = 'layouts/main'): void
    {
        View::render($view, $data, $layout);
    }

    protected function json(array $data, int $status = 200): never
    {
        Response::json($data, $status);
    }

    protected function redirect(string $path, int $status = 302): never
    {
        Response::redirect(url($path), $status);
    }

    protected function back(): never
    {
        Response::back();
    }

    protected function flash(string $key, mixed $value): void
    {
        $_SESSION['_flash'][$key] = $value;
    }

    protected function getFlash(string $key, mixed $default = null): mixed
    {
        if (!isset($_SESSION['_flash'][$key])) {
            return $default;
        }

        $value = $_SESSION['_flash'][$key];
        unset($_SESSION['_flash'][$key]);

        return $value;
    }

    protected function validateCsrf(): void
    {
        $token = $_POST[csrf_token_name()] ?? '';
        $sessionToken = $_SESSION[csrf_token_name()] ?? '';

        if (!is_string($token) || !hash_equals((string) $sessionToken, $token)) {
            http_response_code(419);
            throw new \RuntimeException('Invalid CSRF token.');
        }
    }
}
