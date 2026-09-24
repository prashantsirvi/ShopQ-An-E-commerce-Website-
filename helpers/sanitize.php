<?php

declare(strict_types=1);

/**
 * Sanitize user input for safe storage/display.
 */
function sanitize_string(?string $value): string
{
    return trim(filter_var($value ?? '', FILTER_SANITIZE_SPECIAL_CHARS));
}

function sanitize_email(?string $value): string
{
    return trim(filter_var($value ?? '', FILTER_SANITIZE_EMAIL));
}

function sanitize_int(mixed $value, int $default = 0): int
{
    return filter_var($value, FILTER_VALIDATE_INT) !== false
        ? (int) filter_var($value, FILTER_VALIDATE_INT)
        : $default;
}

function sanitize_float(mixed $value, float $default = 0.0): float
{
    return filter_var($value, FILTER_VALIDATE_FLOAT) !== false
        ? (float) filter_var($value, FILTER_VALIDATE_FLOAT)
        : $default;
}

/**
 * Escape output for HTML contexts.
 */
function e(mixed $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}
