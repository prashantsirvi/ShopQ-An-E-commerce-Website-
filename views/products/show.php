<?php

use Core\View;

$price = (float) $product['display_price'];
$basePrice = (float) $product['base_price'];
$hasDiscount = $price < $basePrice;
$images = $product['images'] ?? [];
$variants = $product['variants'] ?? [];
?>

<section class="product-detail-page">
    <div class="container">
        <?php View::partial('partials/breadcrumb', ['breadcrumb' => $breadcrumb]); ?>

        <div class="product-detail-grid">
            <div class="product-gallery" data-product-gallery>
                <div class="gallery-main">
                    <img
                        src="<?= e(product_image_url($images[0]['image_path'] ?? null)) ?>"
                        alt="<?= e($product['name']) ?>"
                        data-gallery-main
                        class="is-loaded"
                    >
                </div>
                <?php if (count($images) > 1): ?>
                    <div class="gallery-thumbs">
                        <?php foreach ($images as $index => $image): ?>
                            <button
                                type="button"
                                class="gallery-thumb <?= $index === 0 ? 'is-active' : '' ?>"
                                data-gallery-thumb="<?= e(product_image_url($image['image_path'])) ?>"
                            >
                                <img src="<?= e(product_image_url($image['image_path'])) ?>" alt="<?= e($product['name']) ?>">
                            </button>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <div class="product-detail-info">
                <span class="product-category"><?= e($product['category_name']) ?></span>
                <h1><?= e($product['name']) ?></h1>

                <div class="product-rating product-rating-lg">
                    <span class="stars">
                        <?php for ($i = 1; $i <= 5; $i++): ?>
                            <i class="fa-solid fa-star<?= $i <= round((float) $product['rating_avg']) ? '' : ' star-muted' ?>"></i>
                        <?php endfor; ?>
                    </span>
                    <span><?= number_format((float) $product['rating_avg'], 1) ?> (<?= (int) $product['rating_count'] ?> reviews)</span>
                </div>

                <div class="product-price-row product-price-lg">
                    <strong class="product-price"><?= format_money($price) ?></strong>
                    <?php if ($hasDiscount): ?>
                        <span class="product-price-old"><?= format_money($basePrice) ?></span>
                        <span class="product-badge inline-badge"><?= (int) $product['discount_percent'] ?>% OFF</span>
                    <?php endif; ?>
                </div>

                <p class="product-short"><?= e($product['short_description']) ?></p>

                <?php if ($variants !== []): ?>
                    <div class="variant-picker" data-variant-picker>
                        <span class="variant-label">Color</span>
                        <div class="variant-options">
                            <?php foreach ($variants as $index => $variant): ?>
                                <button
                                    type="button"
                                    class="variant-option <?= $index === 0 ? 'is-active' : '' ?>"
                                    data-variant-id="<?= (int) $variant['id'] ?>"
                                    data-price-adjustment="<?= e((string) $variant['price_adjustment']) ?>"
                                    data-stock="<?= (int) $variant['stock'] ?>"
                                    title="<?= e($variant['color_name']) ?>"
                                >
                                    <span class="variant-swatch" style="background: <?= e($variant['color_hex']) ?>"></span>
                                    <?= e($variant['color_name']) ?>
                                </button>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>

                <p class="stock-status" data-stock-status>
                    <?php if ((int) $product['stock'] > 0): ?>
                        <i class="fa-solid fa-circle-check"></i> In stock (<?= (int) $product['stock'] ?> available)
                    <?php else: ?>
                        <i class="fa-solid fa-circle-xmark"></i> Out of stock
                    <?php endif; ?>
                </p>

                <div class="product-actions-row">
                    <div class="qty-picker">
                        <button type="button" data-qty-minus aria-label="Decrease quantity">−</button>
                        <input type="number" value="1" min="1" max="<?= max(1, (int) $product['stock']) ?>" data-qty-input aria-label="Quantity">
                        <button type="button" data-qty-plus aria-label="Increase quantity">+</button>
                    </div>
                    <button type="button" class="btn btn-primary btn-lg" data-add-to-cart="<?= (int) $product['id'] ?>">
                        Add to Cart
                    </button>
                    <button type="button" class="icon-btn" aria-label="Add to wishlist" data-wishlist-add="<?= (int) $product['id'] ?>">
                        <i class="fa-regular fa-heart"></i>
                    </button>
                    <button type="button" class="icon-btn" aria-label="Compare" data-compare-add="<?= (int) $product['id'] ?>">
                        <i class="fa-solid fa-scale-balanced"></i>
                    </button>
                </div>

                <div class="product-meta">
                    <p><strong>SKU:</strong> <?= e($product['sku']) ?></p>
                    <?php if (!empty($product['brand_name'])): ?>
                        <p><strong>Brand:</strong> <?= e($product['brand_name']) ?></p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="product-description-block">
            <h2>Product Description</h2>
            <p><?= nl2br(e($product['description'])) ?></p>
        </div>

        <?php if (!empty($relatedProducts)): ?>
            <section class="home-section">
                <?php View::partial('partials/section-heading', [
                    'title' => 'Related Products',
                    'subtitle' => 'More items from the same category.',
                ]); ?>
                <div class="product-grid product-grid-compact">
                    <?php foreach ($relatedProducts as $item): ?>
                        <?php View::partial('partials/product-card', ['product' => $item]); ?>
                    <?php endforeach; ?>
                </div>
            </section>
        <?php endif; ?>

        <?php if (!empty($recentlyViewed)): ?>
            <section class="home-section">
                <?php View::partial('partials/section-heading', [
                    'title' => 'Recently Viewed',
                    'subtitle' => 'Continue browsing where you left off.',
                ]); ?>
                <div class="product-grid product-grid-compact">
                    <?php foreach ($recentlyViewed as $item): ?>
                        <?php View::partial('partials/product-card', ['product' => $item]); ?>
                    <?php endforeach; ?>
                </div>
            </section>
        <?php endif; ?>
    </div>
</section>
