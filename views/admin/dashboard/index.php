<section class="admin-dashboard">
    <div class="admin-page-header">
        <div>
            <h1>Dashboard</h1>
            <p>Welcome back, <?= e($admin['name'] ?? 'Admin') ?>.</p>
        </div>
    </div>

    <div class="admin-stats">
        <div class="admin-stat-card"><span class="label">Orders</span><strong><?= (int) $summary['orders_total'] ?></strong></div>
        <div class="admin-stat-card"><span class="label">Revenue</span><strong><?= format_money((float) $summary['revenue_total']) ?></strong></div>
        <div class="admin-stat-card"><span class="label">Customers</span><strong><?= (int) $summary['customers_total'] ?></strong></div>
        <div class="admin-stat-card"><span class="label">Products</span><strong><?= (int) $summary['products_total'] ?></strong></div>
        <div class="admin-stat-card"><span class="label">Pending Orders</span><strong><?= (int) $summary['pending_orders'] ?></strong></div>
        <div class="admin-stat-card"><span class="label">Low Stock</span><strong><?= (int) $summary['low_stock'] ?></strong></div>
    </div>

    <div class="admin-charts-grid">
        <div class="admin-panel">
            <h2>Revenue (7 days)</h2>
            <canvas id="revenueChart" height="120" data-analytics-chart="revenue"></canvas>
        </div>
        <div class="admin-panel">
            <h2>Orders by Status</h2>
            <canvas id="statusChart" height="120" data-analytics-chart="status"></canvas>
        </div>
    </div>

    <div class="admin-panels-grid">
        <div class="admin-panel">
            <h2>Top Products</h2>
            <table class="admin-table">
                <thead><tr><th>Product</th><th>Sold</th><th>Revenue</th></tr></thead>
                <tbody>
                    <?php foreach ($topProducts as $row): ?>
                        <tr>
                            <td><?= e($row['product_name']) ?></td>
                            <td><?= (int) $row['qty_sold'] ?></td>
                            <td><?= format_money((float) $row['revenue']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <div class="admin-panel">
            <h2>Trending (7 days)</h2>
            <ul class="admin-list">
                <?php foreach ($trending as $item): ?>
                    <li><strong><?= e($item['name']) ?></strong> — <?= (int) $item['views'] ?> views</li>
                <?php endforeach; ?>
            </ul>
            <h3>Popular Searches</h3>
            <ul class="admin-list">
                <?php foreach ($popularSearches as $search): ?>
                    <li><?= e($search['query']) ?> (<?= (int) $search['searches'] ?>)</li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
</section>
