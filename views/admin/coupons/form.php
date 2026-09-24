<?php $isEdit = $coupon !== null; ?>

<section class="admin-page">
    <div class="admin-page-header"><h1><?= e($title) ?></h1></div>
    <form method="POST" action="<?= $isEdit ? url('/admin/coupons/' . $coupon['id']) : url('/admin/coupons') ?>" class="admin-form admin-panel">
        <?= csrf_field() ?>
        <div class="form-grid checkout-address-grid">
            <div class="form-group"><label for="code">Code *</label><input type="text" id="code" name="code" value="<?= e($coupon['code'] ?? '') ?>" required></div>
            <div class="form-group"><label for="discount_type">Type</label><select id="discount_type" name="discount_type"><option value="percent" <?= ($coupon['discount_type'] ?? '') === 'percent' ? 'selected' : '' ?>>Percent</option><option value="fixed" <?= ($coupon['discount_type'] ?? '') === 'fixed' ? 'selected' : '' ?>>Fixed</option></select></div>
            <div class="form-group"><label for="discount_value">Value *</label><input type="number" step="0.01" id="discount_value" name="discount_value" value="<?= e((string) ($coupon['discount_value'] ?? '')) ?>" required></div>
            <div class="form-group"><label for="min_order_amount">Min order</label><input type="number" step="0.01" id="min_order_amount" name="min_order_amount" value="<?= e((string) ($coupon['min_order_amount'] ?? '0')) ?>"></div>
            <div class="form-group"><label for="max_discount_amount">Max discount</label><input type="number" step="0.01" id="max_discount_amount" name="max_discount_amount" value="<?= e((string) ($coupon['max_discount_amount'] ?? '')) ?>"></div>
            <div class="form-group"><label for="usage_limit">Usage limit</label><input type="number" id="usage_limit" name="usage_limit" value="<?= e((string) ($coupon['usage_limit'] ?? '')) ?>"></div>
            <div class="form-group"><label for="expires_at">Expires at</label><input type="datetime-local" id="expires_at" name="expires_at" value="<?= !empty($coupon['expires_at']) ? e(date('Y-m-d\TH:i', strtotime($coupon['expires_at']))) : '' ?>"></div>
            <div class="form-group form-group-full"><label for="description">Description</label><input type="text" id="description" name="description" value="<?= e($coupon['description'] ?? '') ?>"></div>
            <div class="form-group"><label class="checkbox-label"><input type="checkbox" name="is_active" value="1" <?= !isset($coupon) || !empty($coupon['is_active']) ? 'checked' : '' ?>> Active</label></div>
        </div>
        <button type="submit" class="btn btn-primary"><?= $isEdit ? 'Update' : 'Create' ?></button>
    </form>
</section>
