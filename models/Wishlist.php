<?php

declare(strict_types=1);

namespace Models;

use Core\Model;

final class Wishlist extends Model
{
    private const SESSION_KEY = 'guest_wishlist';
    private const MAX_ITEMS = 50;

    /** @return array<int, array<string, mixed>> */
    public function items(): array
    {
        $productIds = $this->productIds();
        if ($productIds === []) {
            return [];
        }

        $idList = implode(',', $productIds);

        $rows = $this->db->fetchAll(
            "SELECT p.*, c.name AS category_name, c.slug AS category_slug,
                    (SELECT image_path FROM product_images pi
                     WHERE pi.product_id = p.id AND pi.is_primary = 1 LIMIT 1) AS primary_image
             FROM products p
             INNER JOIN categories c ON c.id = p.category_id
             WHERE p.id IN ({$idList}) AND p.is_active = 1 AND p.deleted_at IS NULL"
        );

        foreach ($rows as &$row) {
            $row['display_price'] = effective_price($row);
            $row['image_url'] = product_image_url($row['primary_image'] ?? null);
        }

        return $rows;
    }

    public function count(): int
    {
        return count($this->productIds());
    }

    public function has(int $productId): bool
    {
        return in_array($productId, $this->productIds(), true);
    }

    public function add(int $productId): void
    {
        if ($this->has($productId)) {
            return;
        }

        if ($customer = auth_customer()) {
            $this->db->insert(
                'INSERT IGNORE INTO wishlists (user_id, product_id) VALUES (:user_id, :product_id)',
                ['user_id' => (int) $customer['id'], 'product_id' => $productId]
            );

            return;
        }

        $ids = $this->productIds();
        array_unshift($ids, $productId);
        $_SESSION[self::SESSION_KEY] = array_slice(array_unique($ids), 0, self::MAX_ITEMS);
    }

    public function remove(int $productId): void
    {
        if ($customer = auth_customer()) {
            $this->db->execute(
                'DELETE FROM wishlists WHERE user_id = :user_id AND product_id = :product_id',
                ['user_id' => (int) $customer['id'], 'product_id' => $productId]
            );

            return;
        }

        $_SESSION[self::SESSION_KEY] = array_values(array_filter(
            $this->productIds(),
            static fn ($id) => $id !== $productId
        ));
    }

    public function mergeSessionToUser(int $userId): void
    {
        $ids = array_map('intval', $_SESSION[self::SESSION_KEY] ?? []);
        unset($_SESSION[self::SESSION_KEY]);

        foreach ($ids as $productId) {
            $this->db->insert(
                'INSERT IGNORE INTO wishlists (user_id, product_id) VALUES (:user_id, :product_id)',
                ['user_id' => $userId, 'product_id' => $productId]
            );
        }
    }

    /** @return array<int, int> */
    private function productIds(): array
    {
        if ($customer = auth_customer()) {
            $rows = $this->db->fetchAll(
                'SELECT product_id FROM wishlists WHERE user_id = :user_id ORDER BY created_at DESC',
                ['user_id' => (int) $customer['id']]
            );

            return array_map(static fn ($row) => (int) $row['product_id'], $rows);
        }

        return array_map('intval', $_SESSION[self::SESSION_KEY] ?? []);
    }
}
