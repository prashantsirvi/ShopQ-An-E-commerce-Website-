<?php

$queryParams = $_GET;
unset($queryParams['page']);
$currentPage = (int) ($pagination['page'] ?? 1);
$totalPages = (int) ($pagination['total_pages'] ?? 1);

if ($totalPages <= 1) {
    return;
}

$pages = pagination_window($currentPage, $totalPages);
?>
<nav class="pagination" aria-label="Pagination">
    <?php if ($currentPage > 1): ?>
        <a class="pagination-btn" href="<?= e(query_with_page($basePath, $queryParams, $currentPage - 1)) ?>">Previous</a>
    <?php endif; ?>

    <div class="pagination-pages">
        <?php foreach ($pages as $pageNumber): ?>
            <a
                class="pagination-page <?= $pageNumber === $currentPage ? 'is-active' : '' ?>"
                href="<?= e(query_with_page($basePath, $queryParams, $pageNumber)) ?>"
            ><?= $pageNumber ?></a>
        <?php endforeach; ?>
    </div>

    <?php if ($currentPage < $totalPages): ?>
        <a class="pagination-btn" href="<?= e(query_with_page($basePath, $queryParams, $currentPage + 1)) ?>">Next</a>
    <?php endif; ?>
</nav>
