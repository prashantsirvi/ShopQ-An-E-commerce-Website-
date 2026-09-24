<a href="<?= url('/category/' . ($category['slug'] ?? '')) ?>" class="category-card">
    <span class="category-icon"><i class="fa-solid <?= e($category['icon'] ?: 'fa-tag') ?>"></i></span>
    <span class="category-name"><?= e($category['name']) ?></span>
</a>
