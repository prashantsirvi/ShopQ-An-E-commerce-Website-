<?php

declare(strict_types=1);

namespace Services\Gateways;

use Services\PaymentGatewayInterface;
use Services\PaymentResult;

final class UpiGateway implements PaymentGatewayInterface
{
    public function slug(): string
    {
        return 'upi';
    }

    public function initiate(array $order, array $payment): array
    {
        $upiId = (string) setting('upi_id', 'shopq@upi');
        $amount = (float) $order['total_amount'];
        $note = 'Order ' . $order['order_number'];
        $upiString = sprintf('upi://pay?pa=%s&pn=ShopQ&am=%.2f&cu=INR&tn=%s', $upiId, $amount, rawurlencode($note));

        return [
            'view' => 'payment/upi',
            'order' => $order,
            'payment' => $payment,
            'upi_id' => $upiId,
            'upi_string' => $upiString,
        ];
    }

    public function process(array $order, array $payment, array $input): PaymentResult
    {
        $reference = strtoupper(trim(sanitize_string($input['transaction_ref'] ?? '')));

        if ($reference === '' || strlen($reference) < 6) {
            return new PaymentResult(false, 'Enter a valid UPI transaction reference (6+ characters).', 'failed', 'pending');
        }

        return new PaymentResult(
            true,
            'UPI payment submitted. Order confirmed pending verification.',
            'paid',
            'confirmed',
            $reference,
            ['upi_ref' => $reference, 'verification' => 'manual']
        );
    }
}
