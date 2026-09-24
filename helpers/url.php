<?php

declare(strict_types=1);

/**
 * Build absolute or relative application URLs.
 */
function url(string $path = ''): string
{
    static $baseUrl = null;

    if ($baseUrl === null) {
        $baseUrl = rtrim(env('APP_URL', 'http://localhost/shopq/public'), '/');
    }

    if ($path === '' || $path === '/') {
        return $baseUrl . '/';
    }

    return $baseUrl . '/' . ltrim($path, '/');
}

/**
 * Build asset URLs for CSS, JS, and images in /public/assets.
 */
function asset(string $path): string
{
    return url('assets/' . ltrim($path, '/'));
}

/**
 * Redirect helper for plain PHP contexts.
 */
function redirect(string $path, int $status = 302): never
{
    header('Location: ' . url($path), true, $status);
    exit;
}
