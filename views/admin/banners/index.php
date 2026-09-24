<section class="admin-page">
    <div class="admin-page-header"><h1>Banners</h1><a href="<?= url('/admin/banners/create') ?>" class="btn btn-primary">Add Banner</a></div>
    <div class="admin-panel">
        <table class="admin-table">
            <thead><tr><th>Title</th><th>Placement</th><th>Sort</th><th>Active</th><th></th></tr></thead>
            <tbody>
                <?php foreach ($banners as $banner): ?>
                    <tr>
                        <td><?= e($banner['title']) ?></td>
                        <td><?= e($banner['placement']) ?></td>
                        <td><?= (int) $banner['sort_order'] ?></td>
                        <td><?= !empty($banner['is_active']) ? 'Yes' : 'No' ?></td>
                        <td class="admin-row-actions">
                            <a href="<?= url('/admin/banners/' . $banner['id'] . '/edit') ?>" class="btn btn-outline btn-sm">Edit</a>
                            <form action="<?= url('/admin/banners/' . $banner['id'] . '/delete') ?>" method="POST"><input type="hidden" name="<?= e(csrf_token_name()) ?>" value="<?= e(csrf_token()) ?>"><button type="submit" class="btn btn-outline btn-sm">Delete</button></form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</section>
