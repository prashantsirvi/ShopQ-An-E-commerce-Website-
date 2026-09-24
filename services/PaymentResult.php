<?php

declare(strict_types=1);

namespace Services;

final class PaymentResult
{
    public function __construct(
        public readonly bool $success,
        public readonly string $message,
        public readonly string $paymentStatus = 'pending',
        public readonly string $orderStatus = 'pending',
        public readonly ?string $transactionId = null,
        public readonly array $gatewayResponse = [],
    ) {
    }
}
