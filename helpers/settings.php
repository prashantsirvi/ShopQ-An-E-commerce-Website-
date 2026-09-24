<?php

declare(strict_types=1);

/**
 * Application settings loaded from database with static fallback.
 */
function setting(string $key, mixed $default = null): mixed
{
    static $settings = null;

    if ($settings === null) {
        $settings = [];

        try {
            $rows = Core\Database::getInstance()->fetchAll(
                'SELECT setting_key, setting_value FROM settings'
            );

            foreach ($rows as $row) {
                $settings[$row['setting_key']] = $row['setting_value'];
            }
        } catch (Throwable) {
            $settings = [];
        }
    }

    return $settings[$key] ?? $default;
}

function currency_symbol(): string
{
    return (string) setting('currency_symbol', '₹');
}

function format_money(float|int|string $amount): string
{
    return currency_symbol() . number_format((float) $amount, 0);
}
