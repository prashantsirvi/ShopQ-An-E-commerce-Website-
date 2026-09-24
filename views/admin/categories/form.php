<?php $isEdit = $category !== null; ?>

<section class="admin-page">
    <div class="admin-page-header"><h1><?= e($title) ?></h1></div>
    <form method="POST" action="<?= $isEdit ? url('/admin/categories/' . $category['id']) : url('/admin/categories') ?>" class="admin-form admin-panel">
        <?= csrf_field() ?>
        <div class="form-grid checkout-address-grid">
            <div class="form-group"><label for="name">Name *</label><input type="text" id="name" name="name" value="<?= e($category['name'] ?? '') ?>" required></div>
            <div class="form-group"><label for="sort_order">Sort order</label><input type="number" id="sort_order" name="sort_order" value="<?= e((string) ($category['sort_order'] ?? '0')) ?>"></div>
            <div class="form-group form-group-full"><label for="description">Description</label><textarea id="description" name="description" rows="3"><?= e($category['description'] ?? '') ?></textarea></div>
            <div class="form-group"><label class="checkbox-label"><input type="checkbox" name="is_active" value="1" <?= !isset($category) || !empty($category['is_active']) ? 'checked' : '' ?>> Active</label></div>
        </div>
        <button type="submit" class="btn btn-primary"><?= $isEdit ? 'Update' : 'Create' ?></button>
    </form>
</section>
