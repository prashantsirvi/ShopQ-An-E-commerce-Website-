<?php use Core\View; ?>

<section class="commerce-page checkout-page">
    <div class="container">
        <?php View::partial('partials/breadcrumb', ['breadcrumb' => [
            ['label' => 'Home', 'url' => url('/')],
            ['label' => 'Cart', 'url' => url('/cart')],
            ['label' => 'Checkout', 'url' => null],
        ]]); ?>

        <div class="commerce-header">
            <h1>Checkout</h1>
            <p>Review your order and complete delivery details.</p>
        </div>

        <div class="commerce-layout checkout-layout">
            <div class="commerce-main">
                <section class="checkout-panel">
                    <h2>Delivery Address</h2>

                    <?php if ($addresses !== []): ?>
                        <form method="POST" action="<?= url('/checkout/place-order') ?>" id="checkout-form">
                            <?= csrf_field() ?>
                            <div class="address-list">
                                <?php foreach ($addresses as $address): ?>
                                    <label class="address-card">
                                        <input type="radio" name="address_id" value="<?= (int) $address['id'] ?>" <?= !empty($address['is_default']) ? 'checked' : '' ?>>
                                        <div>
                                            <strong><?= e($address['label']) ?> — <?= e($address['full_name']) ?></strong>
                                            <p><?= e($address['address_line1']) ?><?= $address['address_line2'] ? ', ' . e($address['address_line2']) : '' ?></p>
                                            <p><?= e($address['city']) ?>, <?= e($address['state']) ?> — <?= e($address['postal_code']) ?></p>
                                            <p><?= e($address['phone']) ?></p>
                                        </div>
                                    </label>
                                <?php endforeach; ?>
                            </div>

                            <?php View::partial('partials/checkout-payment', ['summary' => $summary]); ?>
                        </form>
                    <?php else: ?>
                        <form method="POST" action="<?= url('/checkout/place-order') ?>" id="checkout-form">
                            <?= csrf_field() ?>
                            <?php View::partial('partials/checkout-address-form'); ?>
                            <?php View::partial('partials/checkout-payment', ['summary' => $summary]); ?>
                        </form>
                    <?php endif; ?>
                </section>
            </div>

            <aside class="commerce-summary checkout-summary">
                <h2>Order Summary</h2>

                <ul class="checkout-items">
                    <?php foreach ($items as $item): ?>
                        <li>
                            <img src="<?= e($item['image_url']) ?>" alt="">
                            <div>
                                <strong><?= e($item['name']) ?></strong>
                                <?php if (!empty($item['variant_label'])): ?>
                                    <small><?= e($item['variant_label']) ?></small>
                                <?php endif; ?>
                                <small>Qty: <?= (int) $item['quantity'] ?></small>
                            </div>
                            <span><?= format_money((float) $item['line_total']) ?></span>
                        </li>
                    <?php endforeach; ?>
                </ul>

                <form action="<?= url('/checkout/coupon') ?>" method="POST" class="coupon-form">
                    <?= csrf_field() ?>
                    <label for="coupon_code">Coupon code</label>
                    <div class="coupon-row">
                        <input type="text" id="coupon_code" name="coupon_code" value="<?= e($couponCode) ?>" placeholder="e.g. WELCOME10">
                        <button type="submit" class="btn btn-outline">Apply</button>
                    </div>
                    <p class="summary-note">Try: WELCOME10, FLAT100, MEGA20</p>
                </form>

                <dl class="summary-lines">
                    <div><dt>Subtotal</dt><dd><?= format_money((float) $summary['subtotal']) ?></dd></div>
                    <?php if ($summary['discount'] > 0): ?>
                        <div class="summary-discount"><dt>Discount</dt><dd>−<?= format_money((float) $summary['discount']) ?></dd></div>
                    <?php endif; ?>
                    <div><dt>Shipping</dt><dd><?= $summary['shipping'] > 0 ? format_money((float) $summary['shipping']) : 'Free' ?></dd></div>
                    <div><dt>Tax</dt><dd><?= format_money((float) $summary['tax']) ?></dd></div>
                </dl>

                <p class="summary-total">
                    <span>Total</span>
                    <strong><?= format_money((float) $summary['total']) ?></strong>
                </p>
            </aside>
        </div>
    </div>
</section>
