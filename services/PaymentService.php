<?php

declare(strict_types=1);

namespace Services;

use Models\Payment;
use Services\Gateways\CodGateway;
use Services\Gateways\DummyGateway;
use Services\Gateways\RazorpayGateway;
use Services\Gateways\UpiGateway;

final class PaymentService
{
    /** @var array<string, PaymentGatewayInterface> */
    private array $gateways;

    private Payment $payments;

    public function __construct()
    {
        $this->payments = new Payment();
        $this->gateways = [
            'cod' => new CodGateway(),
            'dummy' => new DummyGateway(),
            'upi' => new UpiGateway(),
            'razorpay' => new RazorpayGateway(),
        ];
    }

    public function gateway(string $slug): ?PaymentGatewayInterface
    {
        return $this->gateways[$slug] ?? null;
    }

    public function createForOrder(int $orderId, string $gateway, float $amount): int
    {
        return $this->payments->create($orderId, $gateway, $amount);
    }

    /** @return array<string, mixed> */
    public function initiate(array $order): array
    {
        $gateway = $this->gateway((string) $order['payment_method']);

        if ($gateway === null) {
            throw new \RuntimeException('Unsupported payment method.');
        }

        $payment = $this->payments->findByOrderId((int) $order['id']);

        if ($payment === null) {
            throw new \RuntimeException('Payment record not found.');
        }

        return $gateway->initiate($order, $payment);
    }

    public function process(array $order, array $input): PaymentResult
    {
        $gateway = $this->gateway((string) $order['payment_method']);

        if ($gateway === null) {
            return new PaymentResult(false, 'Unsupported payment method.', 'failed');
        }

        $payment = $this->payments->findByOrderId((int) $order['id']);

        if ($payment === null) {
            return new PaymentResult(false, 'Payment record not found.', 'failed');
        }

        $result = $gateway->process($order, $payment, $input);

        $this->payments->updateStatus(
            (int) $payment['id'],
            $result->paymentStatus === 'paid' ? 'success' : ($result->success ? 'pending' : 'failed'),
            $result->transactionId,
            $result->gatewayResponse
        );

        return $result;
    }
}
