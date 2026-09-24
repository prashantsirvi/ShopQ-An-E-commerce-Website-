<?php

declare(strict_types=1);

namespace Models;

use Core\Model;

final class CompareList extends Model
{
    private const SESSION_KEY = 'guest_compare';
    private const MAX_ITEMS = 4;

    /** @return array<int, array<string, mixed>> */
    public function items(): array
    {
        $productIds = $this->productIds();
        if ($productIds === []) {
            return [];
        }

        $idList = implode(',', $productIds);

        $rows = $this->db->fetchAll(
            "SELECT p.*, c.name AS category_name, b.name AS brand_name,
                    (SELECT image_path FROM product_images pi
                     WHERE pi.product_id = p.id AND pi.is_primary = 1 LIMIT 1) AS primary_image
             FROM products p
             INNER JOIN categories c ON c.id = p.category_id
             LEFT JOIN brands b ON b.id = p.brand_id
             WHERE p.id IN ({$idList}) AND p.is_active = 1 AND p.deleted_at IS NULL"
        );

        $ordered = [];
        foreach ($productIds as $productId) {
            foreach ($rows as $row) {
                if ((int) $row['id'] === $productId) {
                    $row['display_price'] = effective_price($row);
                    $row['image_url'] = product_image_url($row['primary_image'] ?? null);
                    $ordered[] = $row;
                }
            }
        }

        return $ordered;
    }

    public function count(): int
    {
        return count($this->productIds());
    }

    public function has(int $productId): bool
    {
        return in_array($productId, $this->productIds(), true);
    }

    public function add(int $productId): bool
    {
        if ($this->has($productId)) {
            return true;
        }

        if ($this->count() >= self::MAX_ITEMS) {
            return false;
        }

        if ($customer = auth_customer()) {
            $this->db->insert(
                'INSERT INTO compare_items (user_id, session_id, product_id)
                 VALUES (:user_id, NULL, :product_id)',
                ['user_id' => (int) $customer['id'], 'product_id' => $productId]
            );

            return true;
        }

        $ids = $this->productIds();
        $ids[] = $productId;
        $_SESSION[self::SESSION_KEY] = $ids;

        return true;
    }

    public function remove(int $productId): void
    {
        if ($customer = auth_customer()) {
            $this->db->execute(
                'DELETE FROM compare_items WHERE user_id = :user_id AND product_id = :product_id',
                ['user_id' => (int) $customer['id'], 'product_id' => $productId]
            );

            return;
        }

        $_SESSION[self::SESSION_KEY] = array_values(array_filter(
            $this->productIds(),
            static fn ($id) => $id !== $productId
        ));
    }

    public function clear(): void
    {
        if ($customer = auth_customer()) {
            $this->db->execute(
                'DELETE FROM compare_items WHERE user_id = :user_id',
                ['user_id' => (int) $customer['id']]
            );

            return;
        }

        unset($_SESSION[self::SESSION_KEY]);
    }

    public function mergeSessionToUser(int $userId): void
    {
        $ids = array_map('intval', $_SESSION[self::SESSION_KEY] ?? []);
        unset($_SESSION[self::SESSION_KEY]);

        $existing = $this->db->fetchAll(
            'SELECT product_id FROM compare_items WHERE user_id = :user_id',
            ['user_id' => $userId]
        );
        $merged = array_map(static fn ($row) => (int) $row['product_id'], $existing);

        foreach ($ids as $productId) {
            if (count($merged) >= self::MAX_ITEMS) {
                break;
            }

            if (in_array($productId, $merged, true)) {
                continue;
            }

            $this->db->insert(
                'INSERT INTO compare_items (user_id, session_id, product_id) VALUES (:user_id, NULL, :product_id)',
                ['user_id' => $userId, 'product_id' => $productId]
            );
            $merged[] = $productId;
        }
    }

    /** @return array<int, int> */
    private function productIds(): array
    {
        if ($customer = auth_customer()) {
            $rows = $this->db->fetchAll(
                'SELECT product_id FROM compare_items WHERE user_id = :user_id ORDER BY id ASC',
                ['user_id' => (int) $customer['id']]
            );

            return array_map(static fn ($row) => (int) $row['product_id'], $rows);
        }

        return array_map('intval', $_SESSION[self::SESSION_KEY] ?? []);
    }
}
