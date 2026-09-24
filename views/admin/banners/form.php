<?php $isEdit = $banner !== null; ?>

<section class="admin-page">
    <div class="admin-page-header"><h1><?= e($title) ?></h1></div>
    <form method="POST" action="<?= $isEdit ? url('/admin/banners/' . $banner['id']) : url('/admin/banners') ?>" class="admin-form admin-panel">
        <?= csrf_field() ?>
        <div class="form-grid checkout-address-grid">
            <div class="form-group"><label for="title">Title *</label><input type="text" id="title" name="title" value="<?= e($banner['title'] ?? '') ?>" required></div>
            <div class="form-group"><label for="placement">Placement</label><select id="placement" name="placement"><?php foreach (['hero','sidebar','footer','category'] as $p): ?><option value="<?= $p ?>" <?= ($banner['placement'] ?? 'hero') === $p ? 'selected' : '' ?>><?= ucfirst($p) ?></option><?php endforeach; ?></select></div>
            <div class="form-group form-group-full"><label for="subtitle">Subtitle</label><input type="text" id="subtitle" name="subtitle" value="<?= e($banner['subtitle'] ?? '') ?>"></div>
            <div class="form-group form-group-full"><label for="image_path">Image path/URL *</label><input type="text" id="image_path" name="image_path" value="<?= e($banner['image_path'] ?? '') ?>" required></div>
            <div class="form-group form-group-full"><label for="link_url">Link URL</label><input type="text" id="link_url" name="link_url" value="<?= e($banner['link_url'] ?? '') ?>"></div>
            <div class="form-group"><label for="sort_order">Sort order</label><input type="number" id="sort_order" name="sort_order" value="<?= e((string) ($banner['sort_order'] ?? '0')) ?>"></div>
            <div class="form-group"><label class="checkbox-label"><input type="checkbox" name="is_active" value="1" <?= !isset($banner) || !empty($banner['is_active']) ? 'checked' : '' ?>> Active</label></div>
        </div>
        <button type="submit" class="btn btn-primary"><?= $isEdit ? 'Update' : 'Create' ?></button>
    </form>
</section>
