<?php

declare(strict_types=1);

namespace Controllers;

use Models\SettingModel;

final class AdminSettingsController extends AdminController
{
    public function index(): void
    {
        $this->adminView('admin/settings/index', [
            'title' => 'Site Settings',
            'settings' => (new SettingModel())->all(),
        ]);
    }

    public function update(): void
    {
        $this->validateCsrf();

        if (!has_admin_role('admin', 'superadmin')) {
            $this->flash('error', 'Insufficient permissions.');
            $this->redirect('/admin/settings');
        }

        $model = new SettingModel();
        $allowed = [
            'site_name', 'site_tagline', 'support_phone', 'support_email',
            'free_shipping_min', 'shipping_flat_rate', 'tax_percent', 'currency_symbol', 'upi_id',
        ];

        foreach ($allowed as $key) {
            if (isset($_POST[$key])) {
                $model->updateValue($key, sanitize_string((string) $_POST[$key]));
            }
        }

        log_activity('settings.update', 'settings', null);
        $this->flash('success', 'Settings saved.');
        $this->redirect('/admin/settings');
    }
}
