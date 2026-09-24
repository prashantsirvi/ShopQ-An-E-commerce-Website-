<section class="admin-page">
    <div class="admin-page-header"><h1>Order <?= e($order['order_number']) ?></h1></div>
    <div class="admin-panels-grid">
        <div class="admin-panel">
            <h2>Items</h2>
            <table class="admin-table">
                <thead><tr><th>Product</th><th>Qty</th><th>Total</th></tr></thead>
                <tbody>
                    <?php foreach ($order['items'] as $item): ?>
                        <tr>
                            <td><?= e($item['product_name']) ?></td>
                            <td><?= (int) $item['quantity'] ?></td>
                            <td><?= format_money((float) $item['total_price']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <p><strong>Total:</strong> <?= format_money((float) $order['total_amount']) ?></p>
        </div>
        <div class="admin-panel">
            <h2>Update Status</h2>
            <form method="POST" action="<?= url('/admin/orders/' . $order['order_number'] . '/status') ?>">
                <?= csrf_field() ?>
                <div class="form-group">
                    <label for="status">Status</label>
                    <select id="status" name="status">
                        <?php foreach ($statuses as $status): ?>
                            <option value="<?= $status ?>" <?= $order['status'] === $status ? 'selected' : '' ?>><?= ucfirst($status) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="comment">Comment</label>
                    <input type="text" id="comment" name="comment" placeholder="Optional note">
                </div>
                <button type="submit" class="btn btn-primary">Update Status</button>
            </form>
            <h3>History</h3>
            <ul class="admin-list">
                <?php foreach ($order['history'] as $entry): ?>
                    <li><?= e(ucfirst($entry['status'])) ?> — <?= e($entry['comment'] ?? '') ?> <small>(<?= e(date('d M Y H:i', strtotime($entry['created_at']))) ?>)</small></li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
</section>
