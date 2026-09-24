<section class="admin-page">
    <div class="admin-page-header">
        <h1>Reviews</h1>
        <a href="<?= url('/admin/reviews?pending=1') ?>" class="btn btn-outline">Pending only</a>
    </div>
    <div class="admin-panel">
        <table class="admin-table">
            <thead><tr><th>Product</th><th>Customer</th><th>Rating</th><th>Comment</th><th>Approved</th><th></th></tr></thead>
            <tbody>
                <?php foreach ($reviews as $review): ?>
                    <tr>
                        <td><?= e($review['product_name']) ?></td>
                        <td><?= e($review['first_name'] . ' ' . $review['last_name']) ?></td>
                        <td><?= (int) $review['rating'] ?>/5</td>
                        <td><?= e(mb_strimwidth($review['comment'] ?? '', 0, 80, '…')) ?></td>
                        <td><?= !empty($review['is_approved']) ? 'Yes' : 'No' ?></td>
                        <td class="admin-row-actions">
                            <?php if (empty($review['is_approved'])): ?>
                                <form action="<?= url('/admin/reviews/' . $review['id'] . '/approve') ?>" method="POST"><?= csrf_field() ?><button class="btn btn-primary btn-sm">Approve</button></form>
                            <?php endif; ?>
                            <form action="<?= url('/admin/reviews/' . $review['id'] . '/reject') ?>" method="POST"><?= csrf_field() ?><button class="btn btn-outline btn-sm">Reject</button></form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</section>
