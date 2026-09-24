<?php

declare(strict_types=1);

namespace Models;

use Core\Model;

final class Payment extends Model
{
    public function create(int $orderId, string $gateway, float $amount): int
    {
        return $this->db->insert(
            'INSERT INTO payments (order_id, gateway, amount, currency, status)
             VALUES (:order_id, :gateway, :amount, :currency, :status)',
            [
                'order_id' => $orderId,
                'gateway' => $gateway,
                'amount' => $amount,
                'currency' => 'INR',
                'status' => 'pending',
            ]
        );
    }

    public function findByOrderId(int $orderId): ?array
    {
        return $this->db->fetch(
            'SELECT * FROM payments WHERE order_id = :order_id ORDER BY id DESC LIMIT 1',
            ['order_id' => $orderId]
        );
    }

    public function updateStatus(int $paymentId, string $status, ?string $transactionId, array $response = []): void
    {
        $this->db->execute(
            'UPDATE payments
             SET status = :status,
                 transaction_id = :transaction_id,
                 gateway_response = :gateway_response,
                 paid_at = CASE WHEN :status2 = :success THEN NOW() ELSE paid_at END
             WHERE id = :id',
            [
                'status' => $status,
                'status2' => $status,
                'success' => 'success',
                'transaction_id' => $transactionId,
                'gateway_response' => $response === [] ? null : json_encode($response, JSON_THROW_ON_ERROR),
                'id' => $paymentId,
            ]
        );
    }
}
