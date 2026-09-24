<?php

declare(strict_types=1);

/**
 * Return the configured CSRF field name.
 */
function csrf_token_name(): string
{
    static $name = null;

    if ($name === null) {
        $config = require CONFIG_PATH . '/app.php';
        $name = (string) ($config['security']['csrf_token_name'] ?? '_csrf_token');
    }

    return $name;
}

/**
 * Generate or reuse a CSRF token stored in session.
 */
function csrf_token(): string
{
    $name = csrf_token_name();

    if (empty($_SESSION[$name])) {
        $_SESSION[$name] = bin2hex(random_bytes(32));
    }

    return (string) $_SESSION[$name];
}

/**
 * Render a hidden CSRF input for HTML forms.
 */
function csrf_field(): string
{
    $name = csrf_token_name();
    $token = csrf_token();

    return '<input type="hidden" name="' . htmlspecialchars($name, ENT_QUOTES, 'UTF-8') . '" value="' . htmlspecialchars($token, ENT_QUOTES, 'UTF-8') . '">';
}
