<?php use Core\View; ?>

<section class="commerce-page wishlist-page">
    <div class="container">
        <?php View::partial('partials/breadcrumb', ['breadcrumb' => [
            ['label' => 'Home', 'url' => url('/')],
            ['label' => 'Wishlist', 'url' => null],
        ]]); ?>

        <div class="commerce-header">
            <h1>My Wishlist</h1>
            <p>Save products you love and buy them later.</p>
        </div>

        <?php if ($items === []): ?>
            <div class="empty-state">
                <i class="fa-regular fa-heart"></i>
                <h2>Your wishlist is empty</h2>
                <p>Tap the heart icon on any product to save it here.</p>
                <a href="<?= url('/products') ?>" class="btn btn-primary">Browse Products</a>
            </div>
        <?php else: ?>
            <div class="product-grid">
                <?php foreach ($items as $product): ?>
                    <div class="wishlist-card-wrap">
                        <?php View::partial('partials/product-card', ['product' => $product]); ?>
                        <form action="<?= url('/wishlist/remove') ?>" method="POST" class="wishlist-remove-form">
                            <?= csrf_field() ?>
                            <input type="hidden" name="product_id" value="<?= (int) $product['id'] ?>">
                            <input type="hidden" name="redirect" value="<?= e(url('/wishlist')) ?>">
                            <button type="submit" class="btn btn-outline btn-sm btn-block">Remove</button>
                        </form>
                        <form action="<?= url('/cart/add') ?>" method="POST">
                            <?= csrf_field() ?>
                            <input type="hidden" name="product_id" value="<?= (int) $product['id'] ?>">
                            <input type="hidden" name="quantity" value="1">
                            <input type="hidden" name="redirect" value="<?= e(url('/cart')) ?>">
                            <button type="submit" class="btn btn-primary btn-sm btn-block">Add to Cart</button>
                        </form>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>
