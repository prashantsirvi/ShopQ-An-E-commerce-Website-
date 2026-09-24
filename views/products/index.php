<?php use Core\View; ?>

<section class="catalog-page">
    <div class="container">
        <?php View::partial('partials/breadcrumb', ['breadcrumb' => $breadcrumb ?? []]); ?>

        <div class="catalog-header">
            <div>
                <h1><?= e($heading) ?></h1>
                <p><?= e($subtitle) ?></p>
            </div>
            <form method="GET" action="<?= e($basePath) ?>" class="catalog-sort">
                <?php foreach ($_GET as $key => $value): ?>
                    <?php if ($key !== 'sort' && $key !== 'page' && is_string($value)): ?>
                        <input type="hidden" name="<?= e($key) ?>" value="<?= e($value) ?>">
                    <?php endif; ?>
                <?php endforeach; ?>
                <label for="sort">Sort by</label>
                <select id="sort" name="sort" onchange="this.form.submit()">
                    <option value="newest" <?= $sort === 'newest' ? 'selected' : '' ?>>Newest</option>
                    <option value="price_asc" <?= $sort === 'price_asc' ? 'selected' : '' ?>>Price: Low to High</option>
                    <option value="price_desc" <?= $sort === 'price_desc' ? 'selected' : '' ?>>Price: High to Low</option>
                    <option value="popular" <?= $sort === 'popular' ? 'selected' : '' ?>>Popular</option>
                    <option value="rating" <?= $sort === 'rating' ? 'selected' : '' ?>>Top Rated</option>
                </select>
            </form>
        </div>

        <div class="catalog-layout">
            <?php View::partial('partials/catalog-filters', [
                'categories' => $categories,
                'brands' => $brands,
                'filters' => $filters,
                'sort' => $sort,
                'basePath' => $basePath,
                'priceRange' => $priceRange,
                'activeCategory' => $activeCategory ?? null,
                'searchQuery' => $searchQuery ?? null,
            ]); ?>

            <div class="catalog-results">
                <p class="results-count"><?= (int) $pagination['total'] ?> product(s)</p>

                <?php if (empty($products)): ?>
                    <div class="empty-state">
                        <i class="fa-solid fa-box-open"></i>
                        <h2>No products found</h2>
                        <p>Try adjusting filters or search keywords.</p>
                        <a href="<?= url('/products') ?>" class="btn btn-primary">Browse all products</a>
                    </div>
                <?php else: ?>
                    <div class="product-grid">
                        <?php foreach ($products as $product): ?>
                            <?php View::partial('partials/product-card', ['product' => $product]); ?>
                        <?php endforeach; ?>
                    </div>

                    <?php View::partial('partials/pagination', [
                        'pagination' => $pagination,
                        'basePath' => $basePath,
                    ]); ?>
                <?php endif; ?>

                <?php if (!empty($popularSearches)): ?>
                    <div class="popular-searches">
                        <span>Popular:</span>
                        <?php foreach ($popularSearches as $term): ?>
                            <a href="<?= url('/search?q=' . urlencode($term)) ?>"><?= e($term) ?></a>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>
