<?php

declare(strict_types=1);

/**
 * Product session helpers — recently viewed tracking.
 */

function track_recently_viewed(int $productId, int $limit = 10): void
{
    $key = 'recently_viewed';
    $items = $_SESSION[$key] ?? [];

    $items = array_values(array_filter($items, static fn ($id) => (int) $id !== $productId));
    array_unshift($items, $productId);
    $_SESSION[$key] = array_slice($items, 0, $limit);
}

/** @return array<int, int> */
function recently_viewed_ids(): array
{
    return array_map('intval', $_SESSION['recently_viewed'] ?? []);
}
