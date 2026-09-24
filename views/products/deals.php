<?php use Core\View; ?>

<section class="catalog-page deals-page">
    <div class="container">
        <?php View::partial('partials/breadcrumb', ['breadcrumb' => $breadcrumb]); ?>

        <div class="catalog-header">
            <div>
                <h1><?= e($heading) ?></h1>
                <p><?= e($subtitle) ?></p>
            </div>
        </div>

        <?php if (empty($products)): ?>
            <div class="empty-state">
                <h2>No active deals right now</h2>
                <p>Check back soon for new offers.</p>
            </div>
        <?php else: ?>
            <div class="product-grid">
                <?php foreach ($products as $product): ?>
                    <?php View::partial('partials/product-card', ['product' => $product]); ?>
                <?php endforeach; ?>
            </div>

            <?php View::partial('partials/pagination', [
                'pagination' => $pagination,
                'basePath' => url('/deals'),
            ]); ?>
        <?php endif; ?>
    </div>
</section>
