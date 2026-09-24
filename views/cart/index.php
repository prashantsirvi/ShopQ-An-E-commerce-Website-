<?php use Core\View; ?>

<section class="commerce-page cart-page">
    <div class="container">
        <?php View::partial('partials/breadcrumb', ['breadcrumb' => [
            ['label' => 'Home', 'url' => url('/')],
            ['label' => 'Cart', 'url' => null],
        ]]); ?>

        <div class="commerce-header">
            <h1>Shopping Cart</h1>
            <p><?= count($items) ?> item(s) in your cart</p>
        </div>

        <?php if ($items === []): ?>
            <div class="empty-state">
                <i class="fa-solid fa-cart-shopping"></i>
                <h2>Your cart is empty</h2>
                <p>Browse products and add items to get started.</p>
                <a href="<?= url('/products') ?>" class="btn btn-primary">Shop Products</a>
            </div>
        <?php else: ?>
            <div class="commerce-layout">
                <div class="commerce-main">
                    <div class="cart-table">
                        <?php foreach ($items as $item): ?>
                            <article class="cart-row">
                                <a href="<?= url('/products/' . $item['slug']) ?>" class="cart-item-media">
                                    <img src="<?= e($item['image_url']) ?>" alt="<?= e($item['name']) ?>">
                                </a>
                                <div class="cart-item-info">
                                    <span class="product-category"><?= e($item['category_name']) ?></span>
                                    <h3><a href="<?= url('/products/' . $item['slug']) ?>"><?= e($item['name']) ?></a></h3>
                                    <?php if (!empty($item['variant_label'])): ?>
                                        <p class="cart-variant">Color: <?= e($item['variant_label']) ?></p>
                                    <?php endif; ?>
                                    <p class="cart-unit-price"><?= format_money((float) $item['unit_price']) ?> each</p>
                                </div>
                                <form action="<?= url('/cart/update') ?>" method="POST" class="cart-qty-form">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="product_id" value="<?= (int) $item['id'] ?>">
                                    <input type="hidden" name="variant_id" value="<?= (int) ($item['variant_id'] ?? 0) ?>">
                                    <input type="hidden" name="redirect" value="<?= e(url('/cart')) ?>">
                                    <div class="qty-picker">
                                        <button type="button" data-qty-minus aria-label="Decrease">−</button>
                                        <input type="number" name="quantity" value="<?= (int) $item['quantity'] ?>" min="1" max="99" data-qty-input>
                                        <button type="button" data-qty-plus aria-label="Increase">+</button>
                                    </div>
                                    <button type="submit" class="btn btn-outline btn-sm">Update</button>
                                </form>
                                <div class="cart-line-total">
                                    <strong><?= format_money((float) $item['line_total']) ?></strong>
                                </div>
                                <form action="<?= url('/cart/remove') ?>" method="POST" class="cart-remove-form">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="product_id" value="<?= (int) $item['id'] ?>">
                                    <input type="hidden" name="variant_id" value="<?= (int) ($item['variant_id'] ?? 0) ?>">
                                    <input type="hidden" name="redirect" value="<?= e(url('/cart')) ?>">
                                    <button type="submit" class="icon-btn" aria-label="Remove item">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </form>
                            </article>
                        <?php endforeach; ?>
                    </div>
                </div>

                <aside class="commerce-summary">
                    <h2>Order Summary</h2>
                    <dl class="summary-lines">
                        <div><dt>Subtotal</dt><dd><?= format_money($subtotal) ?></dd></div>
                        <div><dt>Shipping</dt><dd>Calculated at checkout</dd></div>
                    </dl>
                    <p class="summary-total">
                        <span>Subtotal</span>
                        <strong><?= format_money($subtotal) ?></strong>
                    </p>
                    <?php if (is_customer_authenticated()): ?>
                        <a href="<?= url('/checkout') ?>" class="btn btn-primary btn-block">Proceed to Checkout</a>
                    <?php else: ?>
                        <a href="<?= url('/login') ?>" class="btn btn-primary btn-block">Login to Checkout</a>
                        <p class="summary-note">Guest cart is saved in your session until you log in.</p>
                    <?php endif; ?>
                    <a href="<?= url('/products') ?>" class="btn btn-outline btn-block">Continue Shopping</a>
                </aside>
            </div>
        <?php endif; ?>
    </div>
</section>
