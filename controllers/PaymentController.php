<?php

declare(strict_types=1);

namespace Controllers;

use Core\Controller;
use Models\Order;
use Services\PaymentService;

final class PaymentController extends Controller
{
    public function show(string $orderNumber): void
    {
        $customer = auth_customer();
        $order = (new Order())->findByNumberForUser($orderNumber, (int) $customer['id']);

        if ($order === null) {
            http_response_code(404);
            $this->view('errors/404', ['title' => 'Order Not Found', 'message' => 'Order not found.']);
            return;
        }

        if ($order['payment_status'] === 'paid' || $order['status'] === 'confirmed') {
            $this->redirect('/checkout/success/' . $orderNumber);
        }

        if ($order['payment_method'] === 'cod') {
            $this->redirect('/checkout/success/' . $orderNumber);
        }

        try {
            $payload = (new PaymentService())->initiate($order);
            $this->view($payload['view'], array_merge($payload, [
                'title' => 'Complete Payment',
            ]));
        } catch (\Throwable $exception) {
            $this->flash('error', $exception->getMessage());
            $this->redirect('/orders/' . $orderNumber);
        }
    }

    public function process(string $orderNumber): void
    {
        $this->validateCsrf();

        $customer = auth_customer();
        $orderModel = new Order();
        $order = $orderModel->findByNumberForUser($orderNumber, (int) $customer['id']);

        if ($order === null) {
            $this->flash('error', 'Order not found.');
            $this->redirect('/orders');
        }

        $result = (new PaymentService())->process($order, $_POST);

        if ($result->success) {
            $orderModel->updatePaymentAndStatus(
                (int) $order['id'],
                $result->paymentStatus,
                $result->orderStatus,
                (int) $customer['id'],
                $result->message
            );

            $this->flash('success', $result->message);
            $this->redirect('/checkout/success/' . $orderNumber);
        }

        $this->flash('error', $result->message);
        $this->redirect('/payment/' . $orderNumber);
    }
}
