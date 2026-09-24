<?php use Core\View; ?>

<section class="commerce-page orders-page">
    <div class="container">
        <?php View::partial('partials/breadcrumb', ['breadcrumb' => [
            ['label' => 'Home', 'url' => url('/')],
            ['label' => 'My Orders', 'url' => null],
        ]]); ?>

        <div class="commerce-header">
            <h1>My Orders</h1>
            <p>Track and manage your purchases.</p>
        </div>

        <?php if ($orders === []): ?>
            <div class="empty-state">
                <i class="fa-solid fa-box"></i>
                <h2>No orders yet</h2>
                <p>When you place an order, it will appear here.</p>
                <a href="<?= url('/products') ?>" class="btn btn-primary">Start Shopping</a>
            </div>
        <?php else: ?>
            <div class="orders-list">
                <?php foreach ($orders as $order): ?>
                    <article class="order-card">
                        <div class="order-card-head">
                            <div>
                                <strong><?= e($order['order_number']) ?></strong>
                                <span class="order-date"><?= e(date('d M Y, h:i A', strtotime($order['placed_at']))) ?></span>
                            </div>
                            <span class="status-badge status-<?= e($order['status']) ?>"><?= e(ucfirst($order['status'])) ?></span>
                        </div>
                        <div class="order-card-body">
                            <p><?= format_money((float) $order['total_amount']) ?> · <?= e(strtoupper($order['payment_method'])) ?> · Payment: <?= e(ucfirst($order['payment_status'])) ?></p>
                        </div>
                        <div class="order-card-actions">
                            <a href="<?= url('/orders/' . $order['order_number']) ?>" class="btn btn-outline btn-sm">View Details</a>
                            <?php if ($order['payment_status'] !== 'paid' && $order['payment_method'] !== 'cod' && $order['status'] === 'pending'): ?>
                                <a href="<?= url('/payment/' . $order['order_number']) ?>" class="btn btn-primary btn-sm">Complete Payment</a>
                            <?php endif; ?>
                            <a href="<?= url('/orders/' . $order['order_number'] . '/invoice') ?>" class="btn btn-outline btn-sm" target="_blank">Invoice</a>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>

            <?php View::partial('partials/pagination', [
                'pagination' => $pagination,
                'basePath' => url('/orders'),
            ]); ?>
        <?php endif; ?>
    </div>
</section>
