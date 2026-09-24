<?php

declare(strict_types=1);

/**
 * Lightweight input validation helpers.
 */

function validate_required(array $data, array $fields): array
{
    $errors = [];

    foreach ($fields as $field => $label) {
        $value = trim((string) ($data[$field] ?? ''));

        if ($value === '') {
            $errors[$field] = "{$label} is required.";
        }
    }

    return $errors;
}

function validate_email_format(string $email): ?string
{
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return 'Please enter a valid email address.';
    }

    return null;
}

function validate_password_strength(string $password): ?string
{
    if (strlen($password) < 8) {
        return 'Password must be at least 8 characters long.';
    }

    if (!preg_match('/[A-Za-z]/', $password) || !preg_match('/\d/', $password)) {
        return 'Password must contain at least one letter and one number.';
    }

    return null;
}

function validate_password_confirmation(string $password, string $confirmation): ?string
{
    if (!hash_equals($password, $confirmation)) {
        return 'Password confirmation does not match.';
    }

    return null;
}

function validate_phone(?string $phone): ?string
{
    if ($phone === null || trim($phone) === '') {
        return null;
    }

    if (!preg_match('/^[6-9]\d{9}$/', preg_replace('/\D/', '', $phone))) {
        return 'Please enter a valid 10-digit Indian mobile number.';
    }

    return null;
}

function old_input(string $key, mixed $default = ''): string
{
    $value = $_SESSION['_old_input'][$key] ?? $default;

    return e(is_string($value) ? $value : (string) $value);
}

function flash_old_input(array $data): void
{
    $_SESSION['_old_input'] = $data;
}

function clear_old_input(): void
{
    unset($_SESSION['_old_input']);
}

function validation_errors(string $field): string
{
    $errors = $_SESSION['_validation_errors'] ?? [];

    return isset($errors[$field]) ? e($errors[$field]) : '';
}

function has_validation_error(string $field): bool
{
    $errors = $_SESSION['_validation_errors'] ?? [];

    return isset($errors[$field]);
}

function flash_validation_errors(array $errors): void
{
    $_SESSION['_validation_errors'] = $errors;
}

function pull_validation_errors(): array
{
    $errors = $_SESSION['_validation_errors'] ?? [];
    unset($_SESSION['_validation_errors']);

    return $errors;
}

function pull_old_input(): array
{
    $old = $_SESSION['_old_input'] ?? [];
    unset($_SESSION['_old_input']);

    return $old;
}
