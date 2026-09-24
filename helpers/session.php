<?php

declare(strict_types=1);

/**
 * Configure secure PHP session settings.
 *
 * @param array<string, mixed> $config
 */
function configureSession(array $config): void
{
    if (session_status() === PHP_SESSION_ACTIVE) {
        return;
    }

    $sessionPath = STORAGE_PATH . '/sessions';

    if (!is_dir($sessionPath)) {
        mkdir($sessionPath, 0755, true);
    }

    session_save_path($sessionPath);
    session_name((string) ($config['name'] ?? 'shopq_session'));

    session_set_cookie_params([
        'lifetime' => (int) ($config['lifetime'] ?? 7200),
        'path' => '/',
        'secure' => (bool) ($config['secure'] ?? false),
        'httponly' => (bool) ($config['httponly'] ?? true),
        'samesite' => (string) ($config['samesite'] ?? 'Lax'),
    ]);

    session_start();
}
