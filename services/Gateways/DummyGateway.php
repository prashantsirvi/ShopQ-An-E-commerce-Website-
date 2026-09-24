<?php

declare(strict_types=1);

namespace Services\Gateways;

use Services\PaymentGatewayInterface;
use Services\PaymentResult;

final class DummyGateway implements PaymentGatewayInterface
{
    public function slug(): string
    {
        return 'dummy';
    }

    public function initiate(array $order, array $payment): array
    {
        return [
            'view' => 'payment/dummy',
            'order' => $order,
            'payment' => $payment,
        ];
    }

    public function process(array $order, array $payment, array $input): PaymentResult
    {
        $simulate = sanitize_string($input['simulate'] ?? 'success');
        $cardNumber = preg_replace('/\D/', '', (string) ($input['card_number'] ?? '')) ?? '';

        if (strlen($cardNumber) < 12) {
            return new PaymentResult(false, 'Enter a valid card number (12+ digits).', 'failed', 'pending');
        }

        if ($simulate === 'fail') {
            return new PaymentResult(
                false,
                'Payment declined by bank (simulated failure).',
                'failed',
                'pending',
                'DUMMY-FAIL-' . strtoupper(substr(bin2hex(random_bytes(4)), 0, 8)),
                ['card_last4' => substr($cardNumber, -4), 'simulate' => 'fail']
            );
        }

        return new PaymentResult(
            true,
            'Payment successful (simulated).',
            'paid',
            'confirmed',
            'DUMMY-OK-' . strtoupper(substr(bin2hex(random_bytes(4)), 0, 8)),
            ['card_last4' => substr($cardNumber, -4), 'simulate' => 'success']
        );
    }
}
