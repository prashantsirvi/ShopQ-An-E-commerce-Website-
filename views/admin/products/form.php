<?php $isEdit = $product !== null; ?>

<section class="admin-page">
    <div class="admin-page-header"><h1><?= e($title) ?></h1></div>

    <form method="POST" action="<?= $isEdit ? url('/admin/products/' . $product['id']) : url('/admin/products') ?>" class="admin-form admin-panel">
        <?= csrf_field() ?>
        <div class="form-grid checkout-address-grid">
            <div class="form-group form-group-full">
                <label for="name">Product name *</label>
                <input type="text" id="name" name="name" value="<?= e($product['name'] ?? '') ?>" required>
            </div>
            <div class="form-group">
                <label for="sku">SKU *</label>
                <input type="text" id="sku" name="sku" value="<?= e($product['sku'] ?? '') ?>" required>
            </div>
            <div class="form-group">
                <label for="category_id">Category *</label>
                <select id="category_id" name="category_id" required>
                    <?php foreach ($categories as $category): ?>
                        <option value="<?= (int) $category['id'] ?>" <?= ($product['category_id'] ?? 0) == $category['id'] ? 'selected' : '' ?>><?= e($category['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="brand_id">Brand</label>
                <select id="brand_id" name="brand_id">
                    <option value="">None</option>
                    <?php foreach ($brands as $brand): ?>
                        <option value="<?= (int) $brand['id'] ?>" <?= ($product['brand_id'] ?? 0) == $brand['id'] ? 'selected' : '' ?>><?= e($brand['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="base_price">Base price *</label>
                <input type="number" step="0.01" id="base_price" name="base_price" value="<?= e((string) ($product['base_price'] ?? '')) ?>" required>
            </div>
            <div class="form-group">
                <label for="sale_price">Sale price</label>
                <input type="number" step="0.01" id="sale_price" name="sale_price" value="<?= e((string) ($product['sale_price'] ?? '')) ?>">
            </div>
            <div class="form-group">
                <label for="stock">Stock</label>
                <input type="number" id="stock" name="stock" value="<?= e((string) ($product['stock'] ?? '0')) ?>">
            </div>
            <div class="form-group">
                <label for="discount_percent">Discount %</label>
                <input type="number" id="discount_percent" name="discount_percent" value="<?= e((string) ($product['discount_percent'] ?? '0')) ?>">
            </div>
            <div class="form-group form-group-full">
                <label for="short_description">Short description</label>
                <input type="text" id="short_description" name="short_description" value="<?= e($product['short_description'] ?? '') ?>">
            </div>
            <div class="form-group form-group-full">
                <label for="description">Description</label>
                <textarea id="description" name="description" rows="5"><?= e($product['description'] ?? '') ?></textarea>
            </div>
            <div class="form-group">
                <label class="checkbox-label"><input type="checkbox" name="is_active" value="1" <?= !isset($product) || !empty($product['is_active']) ? 'checked' : '' ?>> Active</label>
            </div>
            <div class="form-group">
                <label class="checkbox-label"><input type="checkbox" name="is_featured" value="1" <?= !empty($product['is_featured']) ? 'checked' : '' ?>> Featured</label>
            </div>
        </div>
        <button type="submit" class="btn btn-primary"><?= $isEdit ? 'Update Product' : 'Create Product' ?></button>
    </form>
</section>
