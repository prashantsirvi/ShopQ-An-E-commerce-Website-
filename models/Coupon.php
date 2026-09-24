<?php

declare(strict_types=1);

namespace Models;

use Core\Model;

final class Coupon extends Model
{
    public function findValid(string $code): ?array
    {
        return $this->db->fetch(
            'SELECT * FROM coupons
             WHERE code = :code AND is_active = 1
               AND (starts_at IS NULL OR starts_at <= NOW())
               AND (expires_at IS NULL OR expires_at >= NOW())
               AND (usage_limit IS NULL OR used_count < usage_limit)
             LIMIT 1',
            ['code' => strtoupper(trim($code))]
        );
    }

    public function calculateDiscount(array $coupon, float $subtotal): float
    {
        if ($subtotal < (float) $coupon['min_order_amount']) {
            return 0.0;
        }

        $discount = $coupon['discount_type'] === 'fixed'
            ? (float) $coupon['discount_value']
            : $subtotal * ((float) $coupon['discount_value'] / 100);

        if ($coupon['max_discount_amount'] !== null) {
            $discount = min($discount, (float) $coupon['max_discount_amount']);
        }

        return round(min($discount, $subtotal), 2);
    }

    public function incrementUsage(string $code): void
    {
        $this->db->execute(
            'UPDATE coupons SET used_count = used_count + 1 WHERE code = :code',
            ['code' => strtoupper(trim($code))]
        );
    }

    /** @return array<int, array<string, mixed>> */
    public function adminAll(): array
    {
        return $this->db->fetchAll('SELECT * FROM coupons ORDER BY id DESC');
    }

    public function adminFind(int $id): ?array
    {
        return $this->db->fetch('SELECT * FROM coupons WHERE id = :id LIMIT 1', ['id' => $id]);
    }

    public function adminCreate(array $data): int
    {
        return $this->db->insert(
            'INSERT INTO coupons (code, description, discount_type, discount_value, min_order_amount, max_discount_amount, usage_limit, is_active, expires_at)
             VALUES (:code, :description, :discount_type, :discount_value, :min_order_amount, :max_discount_amount, :usage_limit, :is_active, :expires_at)',
            $data
        );
    }

    public function adminUpdate(int $id, array $data): void
    {
        $data['id'] = $id;
        $this->db->execute(
            'UPDATE coupons SET code = :code, description = :description, discount_type = :discount_type, discount_value = :discount_value,
             min_order_amount = :min_order_amount, max_discount_amount = :max_discount_amount, usage_limit = :usage_limit,
             is_active = :is_active, expires_at = :expires_at WHERE id = :id',
            $data
        );
    }
}
