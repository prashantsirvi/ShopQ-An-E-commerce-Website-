<section class="admin-page">
    <div class="admin-page-header"><h1>Site Settings</h1></div>
    <form method="POST" action="<?= url('/admin/settings') ?>" class="admin-form admin-panel">
        <?= csrf_field() ?>
        <?php
        $map = [];
        foreach ($settings as $row) {
            $map[$row['setting_key']] = $row['setting_value'];
        }
        $fields = [
            'site_name' => 'Site name',
            'site_tagline' => 'Tagline',
            'support_phone' => 'Support phone',
            'support_email' => 'Support email',
            'free_shipping_min' => 'Free shipping min amount',
            'shipping_flat_rate' => 'Flat shipping rate',
            'tax_percent' => 'Tax percent',
            'currency_symbol' => 'Currency symbol',
            'upi_id' => 'UPI ID',
        ];
        ?>
        <div class="form-grid checkout-address-grid">
            <?php foreach ($fields as $key => $label): ?>
                <div class="form-group">
                    <label for="<?= e($key) ?>"><?= e($label) ?></label>
                    <input type="text" id="<?= e($key) ?>" name="<?= e($key) ?>" value="<?= e($map[$key] ?? '') ?>">
                </div>
            <?php endforeach; ?>
        </div>
        <button type="submit" class="btn btn-primary">Save Settings</button>
    </form>
</section>
