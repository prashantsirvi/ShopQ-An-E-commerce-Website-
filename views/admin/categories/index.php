<section class="admin-page">
    <div class="admin-page-header">
        <h1>Categories</h1>
        <a href="<?= url('/admin/categories/create') ?>" class="btn btn-primary">Add Category</a>
    </div>
    <div class="admin-panel">
        <table class="admin-table">
            <thead><tr><th>Name</th><th>Slug</th><th>Sort</th><th>Status</th><th></th></tr></thead>
            <tbody>
                <?php foreach ($categories as $category): ?>
                    <tr>
                        <td><?= e($category['name']) ?></td>
                        <td><?= e($category['slug']) ?></td>
                        <td><?= (int) $category['sort_order'] ?></td>
                        <td><?= !empty($category['is_active']) ? 'Active' : 'Hidden' ?></td>
                        <td><a href="<?= url('/admin/categories/' . $category['id'] . '/edit') ?>" class="btn btn-outline btn-sm">Edit</a></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</section>
