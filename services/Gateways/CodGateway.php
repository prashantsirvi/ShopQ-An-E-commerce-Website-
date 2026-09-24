<?php

declare(strict_types=1);

namespace Services\Gateways;

use Services\PaymentGatewayInterface;
use Services\PaymentResult;

final class CodGateway implements PaymentGatewayInterface
{
    public function slug(): string
    {
        return 'cod';
    }

    public function initiate(array $order, array $payment): array
    {
        return [
            'view' => 'payment/cod',
            'order' => $order,
            'payment' => $payment,
        ];
    }

    public function process(array $order, array $payment, array $input): PaymentResult
    {
        return new PaymentResult(
            true,
            'Cash on Delivery order confirmed.',
            'pending',
            'confirmed',
            'COD-' . $order['order_number'],
            ['method' => 'cod']
        );
    }
}
