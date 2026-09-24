<?php

declare(strict_types=1);

namespace Services;

interface PaymentGatewayInterface
{
    public function slug(): string;

    /** @return array<string, mixed> */
    public function initiate(array $order, array $payment): array;

    public function process(array $order, array $payment, array $input): PaymentResult;
}
