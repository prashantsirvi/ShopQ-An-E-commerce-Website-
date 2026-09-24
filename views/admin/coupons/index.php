<section class="admin-page">
    <div class="admin-page-header"><h1>Coupons</h1><a href="<?= url('/admin/coupons/create') ?>" class="btn btn-primary">Add Coupon</a></div>
    <div class="admin-panel">
        <table class="admin-table">
            <thead><tr><th>Code</th><th>Type</th><th>Value</th><th>Min Order</th><th>Used</th><th>Active</th><th></th></tr></thead>
            <tbody>
                <?php foreach ($coupons as $coupon): ?>
                    <tr>
                        <td><?= e($coupon['code']) ?></td>
                        <td><?= e($coupon['discount_type']) ?></td>
                        <td><?= e((string) $coupon['discount_value']) ?></td>
                        <td><?= format_money((float) $coupon['min_order_amount']) ?></td>
                        <td><?= (int) $coupon['used_count'] ?><?= $coupon['usage_limit'] ? ' / ' . (int) $coupon['usage_limit'] : '' ?></td>
                        <td><?= !empty($coupon['is_active']) ? 'Yes' : 'No' ?></td>
                        <td><a href="<?= url('/admin/coupons/' . $coupon['id'] . '/edit') ?>" class="btn btn-outline btn-sm">Edit</a></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</section>
