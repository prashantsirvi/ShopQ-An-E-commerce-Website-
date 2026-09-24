<section class="admin-page">
    <div class="admin-page-header">
        <h1>Products</h1>
        <a href="<?= url('/admin/products/create') ?>" class="btn btn-primary">Add Product</a>
    </div>

    <form method="GET" class="admin-filter-bar">
        <input type="search" name="q" value="<?= e($q) ?>" placeholder="Search products">
        <button type="submit" class="btn btn-outline">Search</button>
    </form>

    <div class="admin-panel">
        <table class="admin-table">
            <thead>
                <tr><th>Name</th><th>SKU</th><th>Category</th><th>Price</th><th>Stock</th><th>Status</th><th></th></tr>
            </thead>
            <tbody>
                <?php foreach ($products as $product): ?>
                    <tr>
                        <td><?= e($product['name']) ?></td>
                        <td><?= e($product['sku']) ?></td>
                        <td><?= e($product['category_name'] ?? '—') ?></td>
                        <td><?= format_money((float) ($product['sale_price'] ?? $product['base_price'])) ?></td>
                        <td><?= (int) $product['stock'] ?></td>
                        <td><?= !empty($product['is_active']) ? 'Active' : 'Hidden' ?></td>
                        <td class="admin-row-actions">
                            <a href="<?= url('/admin/products/' . $product['id'] . '/edit') ?>" class="btn btn-outline btn-sm">Edit</a>
                            <form action="<?= url('/admin/products/' . $product['id'] . '/delete') ?>" method="POST" onsubmit="return confirm('Delete this product?')">
                                <?= csrf_field() ?>
                                <button type="submit" class="btn btn-outline btn-sm">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</section>
