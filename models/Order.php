<?php

declare(strict_types=1);

namespace Models;

use Core\Model;
use Services\PaymentService;

final class Order extends Model
{
    /**
     * @param array<int, array<string, mixed>> $cartItems
     * @return array{order_id: int, order_number: string}
     */
    public function createFromCheckout(int $userId, array $cartItems, array $checkoutData): array
    {
        $subtotal = (float) $checkoutData['subtotal'];
        $discount = (float) $checkoutData['discount_amount'];
        $shipping = (float) $checkoutData['shipping_amount'];
        $tax = (float) $checkoutData['tax_amount'];
        $total = (float) $checkoutData['total_amount'];
        $orderNumber = 'SQ' . date('ymd') . strtoupper(substr(bin2hex(random_bytes(3)), 0, 6));
        $paymentMethod = $checkoutData['payment_method'] ?? 'cod';

        $this->db->beginTransaction();

        try {
            $orderId = $this->db->insert(
                'INSERT INTO orders (
                    order_number, user_id, status, payment_status, payment_method,
                    subtotal, discount_amount, shipping_amount, tax_amount, total_amount,
                    coupon_code, shipping_name, shipping_phone, shipping_address,
                    shipping_city, shipping_state, shipping_postal_code, notes
                ) VALUES (
                    :order_number, :user_id, :status, :payment_status, :payment_method,
                    :subtotal, :discount_amount, :shipping_amount, :tax_amount, :total_amount,
                    :coupon_code, :shipping_name, :shipping_phone, :shipping_address,
                    :shipping_city, :shipping_state, :shipping_postal_code, :notes
                )',
                [
                    'order_number' => $orderNumber,
                    'user_id' => $userId,
                    'status' => $checkoutData['status'] ?? 'pending',
                    'payment_status' => $checkoutData['payment_status'] ?? 'pending',
                    'payment_method' => $paymentMethod,
                    'subtotal' => $subtotal,
                    'discount_amount' => $discount,
                    'shipping_amount' => $shipping,
                    'tax_amount' => $tax,
                    'total_amount' => $total,
                    'coupon_code' => $checkoutData['coupon_code'] ?? null,
                    'shipping_name' => $checkoutData['shipping_name'],
                    'shipping_phone' => $checkoutData['shipping_phone'],
                    'shipping_address' => $checkoutData['shipping_address'],
                    'shipping_city' => $checkoutData['shipping_city'],
                    'shipping_state' => $checkoutData['shipping_state'],
                    'shipping_postal_code' => $checkoutData['shipping_postal_code'],
                    'notes' => $checkoutData['notes'] ?? null,
                ]
            );

            foreach ($cartItems as $item) {
                $this->db->insert(
                    'INSERT INTO order_items (order_id, product_id, variant_id, product_name, variant_label, sku, quantity, unit_price, total_price)
                     VALUES (:order_id, :product_id, :variant_id, :product_name, :variant_label, :sku, :quantity, :unit_price, :total_price)',
                    [
                        'order_id' => $orderId,
                        'product_id' => $item['id'],
                        'variant_id' => $item['variant_id'] ?? null,
                        'product_name' => $item['name'],
                        'variant_label' => $item['variant_label'] ?? null,
                        'sku' => $item['sku'] ?? ('SQ-' . $item['id']),
                        'quantity' => $item['quantity'],
                        'unit_price' => $item['unit_price'],
                        'total_price' => $item['line_total'],
                    ]
                );

                $this->db->execute(
                    'UPDATE products SET stock = GREATEST(0, stock - :qty) WHERE id = :id',
                    ['qty' => $item['quantity'], 'id' => $item['id']]
                );
            }

            $this->addStatusHistory($orderId, (string) ($checkoutData['status'] ?? 'pending'), 'Order placed by customer', $userId);

            if (!empty($checkoutData['coupon_code'])) {
                (new Coupon())->incrementUsage((string) $checkoutData['coupon_code']);
            }

            (new PaymentService())->createForOrder($orderId, $paymentMethod, $total);

            $this->db->commit();

            return ['order_id' => $orderId, 'order_number' => $orderNumber];
        } catch (\Throwable $exception) {
            $this->db->rollBack();
            throw $exception;
        }
    }

    /** @return array{items: array<int, array<string, mixed>>, pagination: array<string, int>} */
    public function listForUser(int $userId, int $page = 1, int $perPage = 10): array
    {
        $total = (int) $this->db->fetch(
            'SELECT COUNT(*) AS total FROM orders WHERE user_id = :user_id',
            ['user_id' => $userId]
        )['total'];

        $pagination = paginate($total, $page, $perPage);

        $items = $this->db->fetchAll(
            "SELECT * FROM orders WHERE user_id = :user_id
             ORDER BY placed_at DESC
             LIMIT {$pagination['per_page']} OFFSET {$pagination['offset']}",
            ['user_id' => $userId]
        );

        return ['items' => $items, 'pagination' => $pagination];
    }

    /** @return array{items: array<int, array<string, mixed>>, pagination: array<string, int>} */
    public function listAdmin(array $filters, int $page = 1, int $perPage = 15): array
    {
        $where = ['1=1'];
        $params = [];

        if (!empty($filters['status'])) {
            $where[] = 'o.status = :status';
            $params['status'] = $filters['status'];
        }

        if (!empty($filters['q'])) {
            $where[] = '(o.order_number LIKE :q OR u.email LIKE :q OR u.first_name LIKE :q)';
            $params['q'] = '%' . $filters['q'] . '%';
        }

        $whereSql = implode(' AND ', $where);

        $total = (int) $this->db->fetch(
            "SELECT COUNT(*) AS total FROM orders o INNER JOIN users u ON u.id = o.user_id WHERE {$whereSql}",
            $params
        )['total'];

        $pagination = paginate($total, $page, $perPage);

        $items = $this->db->fetchAll(
            "SELECT o.*, u.first_name, u.last_name, u.email
             FROM orders o
             INNER JOIN users u ON u.id = o.user_id
             WHERE {$whereSql}
             ORDER BY o.placed_at DESC
             LIMIT {$pagination['per_page']} OFFSET {$pagination['offset']}",
            $params
        );

        return ['items' => $items, 'pagination' => $pagination];
    }

    public function findByNumberForUser(string $orderNumber, int $userId): ?array
    {
        $order = $this->db->fetch(
            'SELECT * FROM orders WHERE order_number = :order_number AND user_id = :user_id LIMIT 1',
            ['order_number' => $orderNumber, 'user_id' => $userId]
        );

        return $order ? $this->hydrateOrder($order) : null;
    }

    public function findByNumber(string $orderNumber): ?array
    {
        $order = $this->db->fetch(
            'SELECT o.*, u.first_name, u.last_name, u.email
             FROM orders o
             INNER JOIN users u ON u.id = o.user_id
             WHERE o.order_number = :order_number
             LIMIT 1',
            ['order_number' => $orderNumber]
        );

        return $order ? $this->hydrateOrder($order) : null;
    }

    public function findById(int $orderId): ?array
    {
        $order = $this->db->fetch('SELECT * FROM orders WHERE id = :id LIMIT 1', ['id' => $orderId]);

        return $order ? $this->hydrateOrder($order) : null;
    }

    public function updatePaymentAndStatus(int $orderId, string $paymentStatus, string $orderStatus, ?int $actorId = null, ?string $comment = null): void
    {
        $this->db->execute(
            'UPDATE orders SET payment_status = :payment_status, status = :status WHERE id = :id',
            ['payment_status' => $paymentStatus, 'status' => $orderStatus, 'id' => $orderId]
        );

        $this->addStatusHistory($orderId, $orderStatus, $comment ?? 'Status updated', $actorId);
    }

    public function updateStatus(int $orderId, string $status, ?int $adminId, ?string $comment = null): void
    {
        $this->db->execute(
            'UPDATE orders SET status = :status WHERE id = :id',
            ['status' => $status, 'id' => $orderId]
        );

        $this->addStatusHistory($orderId, $status, $comment ?? 'Status updated by admin', $adminId);
        log_activity('order.status_update', 'order', $orderId, ['status' => $status]);
    }

    public function countForUser(int $userId): int
    {
        return (int) $this->db->fetch(
            'SELECT COUNT(*) AS total FROM orders WHERE user_id = :user_id',
            ['user_id' => $userId]
        )['total'];
    }

    private function hydrateOrder(array $order): array
    {
        $order['items'] = $this->db->fetchAll(
            'SELECT * FROM order_items WHERE order_id = :order_id',
            ['order_id' => $order['id']]
        );

        $order['history'] = $this->db->fetchAll(
            'SELECT * FROM order_status_history WHERE order_id = :order_id ORDER BY created_at ASC',
            ['order_id' => $order['id']]
        );

        $order['payment'] = $this->db->fetch(
            'SELECT * FROM payments WHERE order_id = :order_id ORDER BY id DESC LIMIT 1',
            ['order_id' => $order['id']]
        );

        return $order;
    }

    private function addStatusHistory(int $orderId, string $status, string $comment, ?int $createdBy): void
    {
        $this->db->insert(
            'INSERT INTO order_status_history (order_id, status, comment, created_by)
             VALUES (:order_id, :status, :comment, :created_by)',
            [
                'order_id' => $orderId,
                'status' => $status,
                'comment' => $comment,
                'created_by' => $createdBy,
            ]
        );
    }
}
