<?php

declare(strict_types=1);

namespace Services\Gateways;

use Services\PaymentGatewayInterface;
use Services\PaymentResult;

/** Razorpay-ready stub — configure RAZORPAY_KEY in .env for live integration. */
final class RazorpayGateway implements PaymentGatewayInterface
{
    public function slug(): string
    {
        return 'razorpay';
    }

    public function initiate(array $order, array $payment): array
    {
        return [
            'view' => 'payment/razorpay',
            'order' => $order,
            'payment' => $payment,
            'configured' => env('RAZORPAY_KEY', '') !== '',
        ];
    }

    public function process(array $order, array $payment, array $input): PaymentResult
    {
        if (env('RAZORPAY_KEY', '') === '') {
            return new PaymentResult(false, 'Razorpay is not configured. Add keys to .env file.', 'failed', 'pending');
        }

        return new PaymentResult(false, 'Razorpay live checkout will be wired in a future release.', 'failed', 'pending');
    }
}
