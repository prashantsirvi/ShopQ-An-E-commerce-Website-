<aside class="catalog-filters">
    <form method="GET" action="<?= e($basePath) ?>" class="filters-form">
        <?php if (!empty($searchQuery)): ?>
            <input type="hidden" name="q" value="<?= e($searchQuery) ?>">
        <?php endif; ?>

        <div class="filter-group">
            <h3>Categories</h3>
            <div class="filter-options">
                <label class="filter-check">
                    <input type="radio" name="category" value="" <?= empty($activeCategory) ? 'checked' : '' ?>
                        onchange="window.location.href='<?= url('/products') ?>'">
                    All Products
                </label>
                <?php foreach ($categories as $category): ?>
                    <label class="filter-check">
                        <input
                            type="radio"
                            name="category"
                            value="<?= e($category['slug']) ?>"
                            <?= (!empty($activeCategory) && (int) $activeCategory['id'] === (int) $category['id']) ? 'checked' : '' ?>
                            onchange="window.location.href='<?= url('/category/' . $category['slug']) ?>'"
                        >
                        <?= e($category['name']) ?>
                    </label>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="filter-group">
            <h3>Brand</h3>
            <select name="brand" class="filter-select" onchange="this.form.submit()">
                <option value="">All brands</option>
                <?php foreach ($brands as $brand): ?>
                    <option
                        value="<?= e($brand['slug']) ?>"
                        <?= (isset($filters['brand_id']) && (int) $filters['brand_id'] === (int) $brand['id']) ? 'selected' : '' ?>
                    ><?= e($brand['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="filter-group">
            <h3>Price Range</h3>
            <div class="price-inputs">
                <input type="number" name="min_price" placeholder="Min" min="0"
                    value="<?= e((string) ($filters['min_price'] ?? '')) ?>">
                <span>—</span>
                <input type="number" name="max_price" placeholder="Max" min="0"
                    value="<?= e((string) ($filters['max_price'] ?? '')) ?>">
            </div>
            <small class="filter-hint">
                Range: <?= format_money($priceRange['min']) ?> – <?= format_money($priceRange['max']) ?>
            </small>
        </div>

        <div class="filter-group">
            <h3>Rating</h3>
            <select name="rating" class="filter-select">
                <option value="">Any rating</option>
                <?php foreach ([4, 3, 2] as $rating): ?>
                    <option value="<?= $rating ?>" <?= (($filters['min_rating'] ?? 0) == $rating) ? 'selected' : '' ?>>
                        <?= $rating ?>★ & above
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="filter-group">
            <label class="filter-check">
                <input type="checkbox" name="in_stock" value="1" <?= !empty($filters['in_stock']) ? 'checked' : '' ?>>
                In stock only
            </label>
        </div>

        <input type="hidden" name="sort" value="<?= e($sort) ?>">
        <button type="submit" class="btn btn-primary btn-block">Apply Filters</button>
        <a href="<?= e($basePath) ?>" class="btn btn-outline btn-block">Clear Filters</a>
    </form>
</aside>
