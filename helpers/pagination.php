<?php

declare(strict_types=1);

/**
 * Pagination helpers for catalog listings.
 *
 * @return array{page: int, per_page: int, total: int, total_pages: int, offset: int}
 */
function paginate(int $total, int $page, int $perPage = 12): array
{
    $perPage = max(1, $perPage);
    $totalPages = max(1, (int) ceil($total / $perPage));
    $page = max(1, min($page, $totalPages));

    return [
        'page' => $page,
        'per_page' => $perPage,
        'total' => $total,
        'total_pages' => $totalPages,
        'offset' => ($page - 1) * $perPage,
    ];
}

function pagination_window(int $current, int $totalPages, int $radius = 2): array
{
    $start = max(1, $current - $radius);
    $end = min($totalPages, $current + $radius);
    $pages = [];

    for ($i = $start; $i <= $end; $i++) {
        $pages[] = $i;
    }

    return $pages;
}

function query_with_page(string $baseUrl, array $params, int $page): string
{
    $params['page'] = $page;
    $query = http_build_query(array_filter($params, static fn ($value) => $value !== '' && $value !== null));

    return $baseUrl . ($query !== '' ? '?' . $query : '');
}
