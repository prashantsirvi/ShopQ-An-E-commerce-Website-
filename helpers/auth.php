<?php

declare(strict_types=1);

/**
 * Authentication and RBAC helpers.
 *
 * Customer and admin sessions are stored in separate namespaces
 * so back-office access never conflicts with shopper sessions.
 */

function auth_customer(): ?array
{
    return $_SESSION['customer'] ?? null;
}

function auth_admin(): ?array
{
    return $_SESSION['admin'] ?? null;
}

function is_customer_authenticated(): bool
{
    return !empty($_SESSION['customer']['id']);
}

function is_admin_authenticated(): bool
{
    return !empty($_SESSION['admin']['id']);
}

function has_admin_role(string ...$roles): bool
{
    $admin = auth_admin();

    if ($admin === null) {
        return false;
    }

    return in_array($admin['role_slug'] ?? '', $roles, true);
}

function login_customer(array $user): void
{
    session_regenerate_id(true);

    $_SESSION['customer'] = [
        'id' => (int) $user['id'],
        'name' => trim(($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? '')),
        'email' => (string) $user['email'],
        'role_id' => (int) $user['role_id'],
        'role_slug' => (string) ($user['role_slug'] ?? 'customer'),
    ];
}

function login_admin(array $user): void
{
    session_regenerate_id(true);

    $_SESSION['admin'] = [
        'id' => (int) $user['id'],
        'name' => trim(($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? '')),
        'email' => (string) $user['email'],
        'role_id' => (int) $user['role_id'],
        'role_slug' => (string) ($user['role_slug'] ?? 'admin'),
    ];
}

function logout_customer(): void
{
    unset($_SESSION['customer']);
    session_regenerate_id(true);
}

function logout_admin(): void
{
    unset($_SESSION['admin']);
    session_regenerate_id(true);
}

function intended_url(string $default = '/'): string
{
    $url = $_SESSION['_intended_url'] ?? $default;
    unset($_SESSION['_intended_url']);

    if (!str_starts_with($url, '/')) {
        return $default;
    }

    return $url;
}

function record_login_attempt(string $scope, string $email): int
{
    $key = strtolower(trim($email));
    $_SESSION['login_attempts'][$scope][$key]['count'] = ($_SESSION['login_attempts'][$scope][$key]['count'] ?? 0) + 1;
    $_SESSION['login_attempts'][$scope][$key]['last_attempt_at'] = time();

    return (int) $_SESSION['login_attempts'][$scope][$key]['count'];
}

function clear_login_attempts(string $scope, string $email): void
{
    unset($_SESSION['login_attempts'][$scope][strtolower(trim($email))]);
}

function is_login_locked(string $scope, string $email, int $maxAttempts = 5, int $lockSeconds = 900): bool
{
    $key = strtolower(trim($email));
    $attempt = $_SESSION['login_attempts'][$scope][$key] ?? null;

    if ($attempt === null) {
        return false;
    }

    if (($attempt['count'] ?? 0) < $maxAttempts) {
        return false;
    }

    $elapsed = time() - (int) ($attempt['last_attempt_at'] ?? 0);

    if ($elapsed >= $lockSeconds) {
        clear_login_attempts($scope, $email);
        return false;
    }

    return true;
}

function login_lockout_remaining(string $scope, string $email, int $maxAttempts = 5, int $lockSeconds = 900): int
{
    if (!is_login_locked($scope, $email, $maxAttempts, $lockSeconds)) {
        return 0;
    }

    $key = strtolower(trim($email));
    $lastAttempt = (int) ($_SESSION['login_attempts'][$scope][$key]['last_attempt_at'] ?? 0);

    return max(0, $lockSeconds - (time() - $lastAttempt));
}
