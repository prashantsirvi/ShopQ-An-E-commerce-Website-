<?php

declare(strict_types=1);

namespace Controllers;

use Models\Order;

final class AdminOrderController extends AdminController
{
    public function index(): void
    {
        $page = max(1, sanitize_int($_GET['page'] ?? 1));
        $result = (new Order())->listAdmin([
            'status' => sanitize_string($_GET['status'] ?? ''),
            'q' => sanitize_string($_GET['q'] ?? ''),
        ], $page);

        $this->adminView('admin/orders/index', [
            'title' => 'Orders',
            'orders' => $result['items'],
            'pagination' => $result['pagination'],
            'filters' => ['status' => $_GET['status'] ?? '', 'q' => $_GET['q'] ?? ''],
        ]);
    }

    public function show(string $orderNumber): void
    {
        $order = (new Order())->findByNumber($orderNumber);

        if ($order === null) {
            $this->flash('error', 'Order not found.');
            $this->redirect('/admin/orders');
        }

        $this->adminView('admin/orders/show', [
            'title' => 'Order ' . $orderNumber,
            'order' => $order,
            'statuses' => ['pending', 'confirmed', 'processing', 'shipped', 'delivered', 'cancelled', 'refunded'],
        ]);
    }

    public function updateStatus(string $orderNumber): void
    {
        $this->validateCsrf();
        $order = (new Order())->findByNumber($orderNumber);

        if ($order === null) {
            $this->flash('error', 'Order not found.');
            $this->redirect('/admin/orders');
        }

        $status = sanitize_string($_POST['status'] ?? '');
        $allowed = ['pending', 'confirmed', 'processing', 'shipped', 'delivered', 'cancelled', 'refunded'];

        if (!in_array($status, $allowed, true)) {
            $this->flash('error', 'Invalid status.');
            $this->redirect('/admin/orders/' . $orderNumber);
        }

        (new Order())->updateStatus((int) $order['id'], $status, (int) auth_admin()['id'], sanitize_string($_POST['comment'] ?? ''));
        $this->flash('success', 'Order status updated.');
        $this->redirect('/admin/orders/' . $orderNumber);
    }
}
