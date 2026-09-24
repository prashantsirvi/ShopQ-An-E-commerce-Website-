<?php

declare(strict_types=1);

/**
 * Application base path derived from SCRIPT_NAME or APP_BASE_PATH in .env.
 *
 * Example: SCRIPT_NAME /shopq/public/index.php → base path /shopq/public
 */
function base_path(): string
{
    static $basePath = null;

    if ($basePath !== null) {
        return $basePath;
    }

    $configured = env('APP_BASE_PATH');

    if ($configured !== null && $configured !== '') {
        $basePath = '/' . trim(str_replace('\\', '/', $configured), '/');

        return $basePath === '/' ? '' : $basePath;
    }

    $scriptName = str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '');
    $basePath = rtrim(dirname($scriptName), '/');

    return $basePath === '/' ? '' : $basePath;
}

/**
 * Strip the application base path from a full request URI path.
 */
function strip_base_path(string $path): string
{
    $path = rawurldecode($path);
    $basePath = base_path();

    if ($basePath !== '' && str_starts_with($path, $basePath)) {
        $path = substr($path, strlen($basePath));
    }

    $path = '/' . trim($path, '/');

    return $path === '/' ? '/' : rtrim($path, '/');
}
