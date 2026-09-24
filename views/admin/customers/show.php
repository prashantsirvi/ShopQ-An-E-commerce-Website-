<section class="admin-page">
    <div class="admin-page-header"><h1><?= e($customer['first_name'] . ' ' . $customer['last_name']) ?></h1></div>
    <div class="admin-panel">
        <p><strong>Email:</strong> <?= e($customer['email']) ?></p>
        <p><strong>Phone:</strong> <?= e($customer['phone'] ?: '—') ?></p>
        <p><strong>Joined:</strong> <?= e(date('d M Y', strtotime($customer['created_at']))) ?></p>
        <h2>Recent Orders</h2>
        <table class="admin-table">
            <thead><tr><th>Order</th><th>Total</th><th>Status</th><th></th></tr></thead>
            <tbody>
                <?php foreach ($orders as $order): ?>
                    <tr>
                        <td><?= e($order['order_number']) ?></td>
                        <td><?= format_money((float) $order['total_amount']) ?></td>
                        <td><?= e(ucfirst($order['status'])) ?></td>
                        <td><a href="<?= url('/admin/orders/' . $order['order_number']) ?>">View</a></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</section>
