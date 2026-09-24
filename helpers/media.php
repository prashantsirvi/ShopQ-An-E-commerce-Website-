<?php

declare(strict_types=1);

/**
 * Media and image URL helpers.
 */
function product_image_url(?string $path): string
{
    if ($path === null || $path === '') {
        return asset('images/product-placeholder.svg');
    }

    if (preg_match('#^https?://#i', $path)) {
        return $path;
    }

    $relative = ltrim($path, '/');
    $publicPath = PUBLIC_PATH . '/' . $relative;
    $rootPath = ROOT_PATH . '/' . $relative;

    if (is_file($publicPath) || is_file($rootPath)) {
        return url($relative);
    }

    return asset('images/product-placeholder.svg');
}

function is_active_nav(string $path): string
{
    $current = strip_base_path(strtok($_SERVER['REQUEST_URI'] ?? '/', '?') ?: '/');
    $target = rtrim($path, '/') ?: '/';

    if ($target === '/' && $current === '/') {
        return 'is-active';
    }

    if ($target !== '/' && str_starts_with($current, $target)) {
        return 'is-active';
    }

    return '';
}

function discount_badge(int $percent): string
{
    if ($percent <= 0) {
        return '';
    }

    return $percent . '% OFF';
}

function effective_price(array $product): float
{
    $sale = $product['sale_price'] ?? null;

    if ($sale !== null && (float) $sale > 0) {
        return (float) $sale;
    }

    return (float) $product['base_price'];
}
