<?php use Core\View; ?>

<section class="commerce-page order-detail-page">
    <div class="container">
        <?php View::partial('partials/breadcrumb', ['breadcrumb' => [
            ['label' => 'Home', 'url' => url('/')],
            ['label' => 'My Orders', 'url' => url('/orders')],
            ['label' => $order['order_number'], 'url' => null],
        ]]); ?>

        <div class="commerce-header order-detail-header">
            <div>
                <h1>Order <?= e($order['order_number']) ?></h1>
                <p>Placed on <?= e(date('d M Y, h:i A', strtotime($order['placed_at']))) ?></p>
            </div>
            <div class="order-detail-actions">
                <a href="<?= url('/orders/' . $order['order_number'] . '/invoice') ?>" class="btn btn-outline" target="_blank">Download Invoice</a>
                <?php if ($order['payment_status'] !== 'paid' && $order['payment_method'] !== 'cod' && in_array($order['status'], ['pending'], true)): ?>
                    <a href="<?= url('/payment/' . $order['order_number']) ?>" class="btn btn-primary">Pay Now</a>
                <?php endif; ?>
            </div>
        </div>

        <div class="order-tracking">
            <?php foreach ($statusSteps as $step): ?>
                <?php
                $stepIndex = array_search($step, $statusSteps, true);
                $currentIndex = array_search($order['status'], $statusSteps, true);
                $isDone = $currentIndex !== false && $stepIndex !== false && $stepIndex <= $currentIndex;
                $isCancelled = $order['status'] === 'cancelled';
                ?>
                <div class="tracking-step <?= $isDone && !$isCancelled ? 'is-done' : '' ?> <?= $order['status'] === $step ? 'is-current' : '' ?>">
                    <span class="tracking-dot"></span>
                    <span><?= e(ucfirst($step)) ?></span>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="commerce-layout">
            <div class="commerce-main">
                <section class="checkout-panel">
                    <h2>Items</h2>
                    <ul class="checkout-items">
                        <?php foreach ($order['items'] as $item): ?>
                            <li>
                                <div>
                                    <strong><?= e($item['product_name']) ?></strong>
                                    <?php if ($item['variant_label']): ?>
                                        <small><?= e($item['variant_label']) ?></small>
                                    <?php endif; ?>
                                    <small>Qty: <?= (int) $item['quantity'] ?> × <?= format_money((float) $item['unit_price']) ?></small>
                                </div>
                                <span><?= format_money((float) $item['total_price']) ?></span>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </section>

                <section class="checkout-panel">
                    <h2>Status History</h2>
                    <ul class="timeline-list">
                        <?php foreach ($order['history'] as $entry): ?>
                            <li>
                                <strong><?= e(ucfirst($entry['status'])) ?></strong>
                                <span><?= e($entry['comment'] ?? '') ?></span>
                                <small><?= e(date('d M Y, h:i A', strtotime($entry['created_at']))) ?></small>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </section>
            </div>

            <aside class="commerce-summary">
                <h2>Summary</h2>
                <dl class="summary-lines">
                    <div><dt>Status</dt><dd><?= e(ucfirst($order['status'])) ?></dd></div>
                    <div><dt>Payment</dt><dd><?= e(ucfirst($order['payment_status'])) ?> (<?= e(strtoupper($order['payment_method'])) ?>)</dd></div>
                    <div><dt>Subtotal</dt><dd><?= format_money((float) $order['subtotal']) ?></dd></div>
                    <?php if ($order['discount_amount'] > 0): ?>
                        <div><dt>Discount</dt><dd>−<?= format_money((float) $order['discount_amount']) ?></dd></div>
                    <?php endif; ?>
                    <div><dt>Shipping</dt><dd><?= $order['shipping_amount'] > 0 ? format_money((float) $order['shipping_amount']) : 'Free' ?></dd></div>
                    <div><dt>Tax</dt><dd><?= format_money((float) $order['tax_amount']) ?></dd></div>
                </dl>
                <p class="summary-total"><span>Total</span><strong><?= format_money((float) $order['total_amount']) ?></strong></p>

                <h3>Delivery</h3>
                <p><?= e($order['shipping_name']) ?><br><?= e($order['shipping_phone']) ?><br>
                <?= e($order['shipping_address']) ?><br>
                <?= e($order['shipping_city']) ?>, <?= e($order['shipping_state']) ?> — <?= e($order['shipping_postal_code']) ?></p>
            </aside>
        </div>
    </div>
</section>
