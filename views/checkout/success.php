<section class="commerce-page checkout-success-page">
    <div class="container">
        <div class="success-card">
            <div class="success-icon"><i class="fa-solid fa-circle-check"></i></div>
            <h1>Order Placed Successfully!</h1>
            <p>Thank you for shopping with <?= e(setting('site_name', 'ShopQ')) ?>.</p>

            <dl class="success-details">
                <div><dt>Order number</dt><dd><strong><?= e($order['order_number']) ?></strong></dd></div>
                <div><dt>Status</dt><dd><?= e(ucfirst($order['status'])) ?></dd></div>
                <div><dt>Payment</dt><dd><?= e(strtoupper($order['payment_method'])) ?> — <?= e(ucfirst($order['payment_status'])) ?></dd></div>
                <div><dt>Total paid</dt><dd><?= format_money((float) $order['total_amount']) ?></dd></div>
                <div><dt>Deliver to</dt><dd><?= e($order['shipping_name']) ?>, <?= e($order['shipping_city']) ?></dd></div>
            </dl>

            <?php if (!empty($order['items'])): ?>
                <div class="success-items">
                    <h2>Items</h2>
                    <ul>
                        <?php foreach ($order['items'] as $item): ?>
                            <li>
                                <span><?= e($item['product_name']) ?><?= $item['variant_label'] ? ' (' . e($item['variant_label']) . ')' : '' ?></span>
                                <span>× <?= (int) $item['quantity'] ?></span>
                                <strong><?= format_money((float) $item['total_price']) ?></strong>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <div class="success-actions">
                <a href="<?= url('/products') ?>" class="btn btn-primary">Continue Shopping</a>
                <a href="<?= url('/profile') ?>" class="btn btn-outline">View Profile</a>
            </div>
        </div>
    </div>
</section>
