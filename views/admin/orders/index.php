<section class="admin-page">
    <div class="admin-page-header"><h1>Orders</h1></div>
    <form method="GET" class="admin-filter-bar">
        <input type="search" name="q" value="<?= e($filters['q']) ?>" placeholder="Order # or email">
        <select name="status">
            <option value="">All statuses</option>
            <?php foreach (['pending','confirmed','processing','shipped','delivered','cancelled'] as $status): ?>
                <option value="<?= $status ?>" <?= $filters['status'] === $status ? 'selected' : '' ?>><?= ucfirst($status) ?></option>
            <?php endforeach; ?>
        </select>
        <button type="submit" class="btn btn-outline">Filter</button>
    </form>
    <div class="admin-panel">
        <table class="admin-table">
            <thead><tr><th>Order</th><th>Customer</th><th>Total</th><th>Status</th><th>Payment</th><th>Date</th><th></th></tr></thead>
            <tbody>
                <?php foreach ($orders as $order): ?>
                    <tr>
                        <td><?= e($order['order_number']) ?></td>
                        <td><?= e($order['first_name'] . ' ' . $order['last_name']) ?></td>
                        <td><?= format_money((float) $order['total_amount']) ?></td>
                        <td><?= e(ucfirst($order['status'])) ?></td>
                        <td><?= e(ucfirst($order['payment_status'])) ?></td>
                        <td><?= e(date('d M Y', strtotime($order['placed_at']))) ?></td>
                        <td><a href="<?= url('/admin/orders/' . $order['order_number']) ?>" class="btn btn-outline btn-sm">View</a></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</section>
