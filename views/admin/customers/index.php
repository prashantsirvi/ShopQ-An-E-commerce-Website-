<section class="admin-page">
    <div class="admin-page-header"><h1>Customers</h1></div>
    <form method="GET" class="admin-filter-bar"><input type="search" name="q" value="<?= e($q) ?>" placeholder="Search customers"><button type="submit" class="btn btn-outline">Search</button></form>
    <div class="admin-panel">
        <table class="admin-table">
            <thead><tr><th>Name</th><th>Email</th><th>Orders</th><th>Joined</th><th></th></tr></thead>
            <tbody>
                <?php foreach ($customers as $customer): ?>
                    <tr>
                        <td><?= e($customer['first_name'] . ' ' . $customer['last_name']) ?></td>
                        <td><?= e($customer['email']) ?></td>
                        <td><?= (int) $customer['orders_count'] ?></td>
                        <td><?= e(date('d M Y', strtotime($customer['created_at']))) ?></td>
                        <td><a href="<?= url('/admin/customers/' . $customer['id']) ?>" class="btn btn-outline btn-sm">View</a></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</section>
