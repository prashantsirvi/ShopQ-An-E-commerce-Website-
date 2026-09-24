<?php

declare(strict_types=1);

namespace Controllers;

use Models\Order;
use Models\User;

final class AdminCustomerController extends AdminController
{
    public function index(): void
    {
        $page = max(1, sanitize_int($_GET['page'] ?? 1));
        $result = (new User())->listCustomers(sanitize_string($_GET['q'] ?? ''), $page);

        $this->adminView('admin/customers/index', [
            'title' => 'Customers',
            'customers' => $result['items'],
            'pagination' => $result['pagination'],
            'q' => $_GET['q'] ?? '',
        ]);
    }

    public function show(int $id): void
    {
        $user = (new User())->findById($id);

        if ($user === null || $user['role_slug'] !== 'customer') {
            $this->flash('error', 'Customer not found.');
            $this->redirect('/admin/customers');
        }

        $orders = (new Order())->listForUser($id, 1, 10);

        $this->adminView('admin/customers/show', [
            'title' => 'Customer Details',
            'customer' => $user,
            'orders' => $orders['items'],
        ]);
    }
}
