<?php

declare(strict_types=1);

namespace Controllers;

use Models\Coupon;

final class AdminCouponController extends AdminController
{
    public function index(): void
    {
        $this->adminView('admin/coupons/index', [
            'title' => 'Coupons',
            'coupons' => (new Coupon())->adminAll(),
        ]);
    }

    public function create(): void
    {
        $this->adminView('admin/coupons/form', ['title' => 'Add Coupon', 'coupon' => null]);
    }

    public function store(): void
    {
        $this->validateCsrf();
        (new Coupon())->adminCreate($this->validated());
        $this->flash('success', 'Coupon created.');
        $this->redirect('/admin/coupons');
    }

    public function edit(int $id): void
    {
        $coupon = (new Coupon())->adminFind($id);

        if ($coupon === null) {
            $this->flash('error', 'Coupon not found.');
            $this->redirect('/admin/coupons');
        }

        $this->adminView('admin/coupons/form', ['title' => 'Edit Coupon', 'coupon' => $coupon]);
    }

    public function update(int $id): void
    {
        $this->validateCsrf();
        (new Coupon())->adminUpdate($id, $this->validated());
        $this->flash('success', 'Coupon updated.');
        $this->redirect('/admin/coupons');
    }

    private function validated(): array
    {
        return [
            'code' => strtoupper(sanitize_string($_POST['code'] ?? '')),
            'description' => sanitize_string($_POST['description'] ?? ''),
            'discount_type' => in_array($_POST['discount_type'] ?? '', ['percent', 'fixed'], true) ? $_POST['discount_type'] : 'percent',
            'discount_value' => (float) ($_POST['discount_value'] ?? 0),
            'min_order_amount' => (float) ($_POST['min_order_amount'] ?? 0),
            'max_discount_amount' => $_POST['max_discount_amount'] !== '' ? (float) $_POST['max_discount_amount'] : null,
            'usage_limit' => $_POST['usage_limit'] !== '' ? sanitize_int($_POST['usage_limit']) : null,
            'is_active' => isset($_POST['is_active']) ? 1 : 0,
            'expires_at' => sanitize_string($_POST['expires_at'] ?? '') ?: null,
        ];
    }
}
