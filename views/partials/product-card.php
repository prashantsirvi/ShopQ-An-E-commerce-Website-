<?php

$price = (float) ($product['display_price'] ?? effective_price($product));
$basePrice = (float) $product['base_price'];
$hasDiscount = $price < $basePrice;
$discount = (int) ($product['discount_percent'] ?? 0);
$inCart = cart_has_product((int) $product['id']);
?>
<article class="product-card">
    <a href="<?= url('/products/' . ($product['slug'] ?? '')) ?>" class="product-card-media">
        <?php if ($discount > 0): ?>
            <span class="product-badge"><?= e(discount_badge($discount)) ?></span>
        <?php endif; ?>
        <img
            src="<?= e($product['image_url'] ?? product_image_url($product['primary_image'] ?? null)) ?>"
            alt="<?= e($product['name']) ?>"
            loading="lazy"
            class="lazy-image"
        >
        <div class="product-card-actions">
            <button type="button" class="icon-btn" aria-label="Add to wishlist" data-wishlist-add="<?= (int) $product['id'] ?>">
                <i class="fa-regular fa-heart"></i>
            </button>
        </div>
    </a>
    <div class="product-card-body">
        <span class="product-category"><?= e($product['category_name'] ?? '') ?></span>
        <h3 class="product-title">
            <a href="<?= url('/products/' . ($product['slug'] ?? '')) ?>"><?= e($product['name']) ?></a>
        </h3>
        <div class="product-rating">
            <span class="stars">
                <?php for ($i = 1; $i <= 5; $i++): ?>
                    <i class="fa-solid fa-star<?= $i <= round((float) $product['rating_avg']) ? '' : ' star-muted' ?>"></i>
                <?php endfor; ?>
            </span>
            <span>(<?= (int) $product['rating_count'] ?>)</span>
        </div>
        <div class="product-card-footer">
            <div class="product-price-row">
                <strong class="product-price"><?= format_money($price) ?></strong>
                <?php if ($hasDiscount): ?>
                    <span class="product-price-old"><?= format_money($basePrice) ?></span>
                <?php endif; ?>
            </div>
            <button
                type="button"
                class="btn btn-sm product-card-cart <?= $inCart ? 'btn-success is-in-cart' : 'btn-primary' ?>"
                data-cart-toggle="<?= (int) $product['id'] ?>"
                aria-pressed="<?= $inCart ? 'true' : 'false' ?>"
                aria-label="<?= $inCart ? 'Remove from cart' : 'Add to cart' ?>"
            >
                <i class="fa-solid <?= $inCart ? 'fa-check' : 'fa-cart-plus' ?>"></i>
                <span><?= $inCart ? 'Added' : 'Add' ?></span>
            </button>
        </div>
    </div>
</article>
