<?php

declare(strict_types=1);

namespace Controllers;

use Core\Controller;
use Models\Order;

final class OrderController extends Controller
{
    public function index(): void
    {
        $customer = auth_customer();
        $page = max(1, sanitize_int($_GET['page'] ?? 1));
        $result = (new Order())->listForUser((int) $customer['id'], $page);

        $this->view('orders/index', [
            'title' => 'My Orders',
            'orders' => $result['items'],
            'pagination' => $result['pagination'],
        ]);
    }

    public function show(string $orderNumber): void
    {
        $customer = auth_customer();
        $order = (new Order())->findByNumberForUser($orderNumber, (int) $customer['id']);

        if ($order === null) {
            http_response_code(404);
            $this->view('errors/404', ['title' => 'Order Not Found', 'message' => 'We could not find this order.']);
            return;
        }

        $this->view('orders/show', [
            'title' => 'Order ' . $orderNumber,
            'order' => $order,
            'statusSteps' => $this->statusSteps(),
        ]);
    }

    public function invoice(string $orderNumber): void
    {
        $customer = auth_customer();
        $order = (new Order())->findByNumberForUser($orderNumber, (int) $customer['id']);

        if ($order === null) {
            http_response_code(404);
            $this->view('errors/404', ['title' => 'Invoice Not Found', 'message' => 'Order not found.']);
            return;
        }

        $this->view('orders/invoice', [
            'title' => 'Invoice ' . $orderNumber,
            'order' => $order,
        ], null);
    }

    /** @return array<int, string> */
    private function statusSteps(): array
    {
        return ['pending', 'confirmed', 'processing', 'shipped', 'delivered'];
    }
}
