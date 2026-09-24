<?php

declare(strict_types=1);

namespace Models;

use Core\Model;

/**
 * Shopping cart — session for guests, database for logged-in customers.
 */
final class Cart extends Model
{
    private const SESSION_KEY = 'guest_cart';

    /** @return array<int, array<string, mixed>> */
    public function items(): array
    {
        if ($customer = auth_customer()) {
            return $this->hydrateItems($this->dbItems((int) $customer['id']));
        }

        return $this->hydrateItems($this->sessionItems());
    }

    public function count(): int
    {
        $total = 0;
        foreach ($this->rawItems() as $item) {
            $total += (int) $item['quantity'];
        }

        return $total;
    }

    public function subtotal(): float
    {
        $total = 0.0;
        foreach ($this->items() as $item) {
            $total += (float) $item['line_total'];
        }

        return round($total, 2);
    }

    public function add(int $productId, ?int $variantId, int $quantity = 1): void
    {
        $quantity = max(1, $quantity);
        $product = $this->getProductForCart($productId, $variantId);
        $unitPrice = (float) $product['unit_price'];

        if ($customer = auth_customer()) {
            $cartId = $this->getOrCreateDbCart((int) $customer['id']);
            $existing = $this->db->fetch(
                'SELECT id, quantity FROM cart_items
                 WHERE cart_id = :cart_id AND product_id = :product_id AND variant_id <=> :variant_id
                 LIMIT 1',
                ['cart_id' => $cartId, 'product_id' => $productId, 'variant_id' => $variantId]
            );

            if ($existing) {
                $this->db->execute(
                    'UPDATE cart_items SET quantity = quantity + :qty, unit_price = :price WHERE id = :id',
                    ['qty' => $quantity, 'price' => $unitPrice, 'id' => $existing['id']]
                );
            } else {
                $this->db->insert(
                    'INSERT INTO cart_items (cart_id, product_id, variant_id, quantity, unit_price)
                     VALUES (:cart_id, :product_id, :variant_id, :quantity, :unit_price)',
                    [
                        'cart_id' => $cartId,
                        'product_id' => $productId,
                        'variant_id' => $variantId,
                        'quantity' => $quantity,
                        'unit_price' => $unitPrice,
                    ]
                );
            }

            return;
        }

        $items = $this->sessionItems();
        $found = false;

        foreach ($items as &$item) {
            if ((int) $item['product_id'] === $productId && ($item['variant_id'] ?? null) == $variantId) {
                $item['quantity'] += $quantity;
                $item['unit_price'] = $unitPrice;
                $found = true;
                break;
            }
        }

        if (!$found) {
            $items[] = [
                'product_id' => $productId,
                'variant_id' => $variantId,
                'quantity' => $quantity,
                'unit_price' => $unitPrice,
            ];
        }

        $_SESSION[self::SESSION_KEY] = $items;
    }

    public function update(int $productId, ?int $variantId, int $quantity): void
    {
        if ($quantity <= 0) {
            $this->remove($productId, $variantId);
            return;
        }

        if ($customer = auth_customer()) {
            $cartId = $this->getDbCartId((int) $customer['id']);
            if ($cartId === null) {
                return;
            }

            $this->db->execute(
                'UPDATE cart_items SET quantity = :quantity
                 WHERE cart_id = :cart_id AND product_id = :product_id AND variant_id <=> :variant_id',
                [
                    'quantity' => $quantity,
                    'cart_id' => $cartId,
                    'product_id' => $productId,
                    'variant_id' => $variantId,
                ]
            );

            return;
        }

        $items = $this->sessionItems();

        foreach ($items as &$item) {
            if ((int) $item['product_id'] === $productId && ($item['variant_id'] ?? null) == $variantId) {
                $item['quantity'] = $quantity;
            }
        }

        unset($item);
        $_SESSION[self::SESSION_KEY] = $items;
    }

    public function remove(int $productId, ?int $variantId): void
    {
        if ($customer = auth_customer()) {
            $cartId = $this->getDbCartId((int) $customer['id']);
            if ($cartId === null) {
                return;
            }

            $this->db->execute(
                'DELETE FROM cart_items
                 WHERE cart_id = :cart_id AND product_id = :product_id AND variant_id <=> :variant_id',
                ['cart_id' => $cartId, 'product_id' => $productId, 'variant_id' => $variantId]
            );

            return;
        }

        $_SESSION[self::SESSION_KEY] = array_values(array_filter(
            $this->sessionItems(),
            static fn ($item) => !((int) $item['product_id'] === $productId && ($item['variant_id'] ?? null) == $variantId)
        ));
    }

    public function clear(): void
    {
        if ($customer = auth_customer()) {
            $cartId = $this->getDbCartId((int) $customer['id']);
            if ($cartId !== null) {
                $this->db->execute('DELETE FROM cart_items WHERE cart_id = :cart_id', ['cart_id' => $cartId]);
            }

            return;
        }

        unset($_SESSION[self::SESSION_KEY]);
    }

    public function mergeSessionToUser(int $userId): void
    {
        $sessionItems = $this->sessionItems();
        unset($_SESSION[self::SESSION_KEY]);

        if ($sessionItems === []) {
            return;
        }

        $cartId = $this->getOrCreateDbCart($userId);

        foreach ($sessionItems as $item) {
            $productId = (int) $item['product_id'];
            $variantId = $item['variant_id'] ?? null;
            $quantity = (int) $item['quantity'];
            $product = $this->getProductForCart($productId, $variantId);
            $unitPrice = (float) $product['unit_price'];

            $existing = $this->db->fetch(
                'SELECT id, quantity FROM cart_items
                 WHERE cart_id = :cart_id AND product_id = :product_id AND variant_id <=> :variant_id
                 LIMIT 1',
                ['cart_id' => $cartId, 'product_id' => $productId, 'variant_id' => $variantId]
            );

            if ($existing) {
                $this->db->execute(
                    'UPDATE cart_items SET quantity = quantity + :qty, unit_price = :price WHERE id = :id',
                    ['qty' => $quantity, 'price' => $unitPrice, 'id' => $existing['id']]
                );
            } else {
                $this->db->insert(
                    'INSERT INTO cart_items (cart_id, product_id, variant_id, quantity, unit_price)
                     VALUES (:cart_id, :product_id, :variant_id, :quantity, :unit_price)',
                    [
                        'cart_id' => $cartId,
                        'product_id' => $productId,
                        'variant_id' => $variantId,
                        'quantity' => $quantity,
                        'unit_price' => $unitPrice,
                    ]
                );
            }
        }
    }

    /** @return array<int, array<string, mixed>> */
    private function rawItems(): array
    {
        if ($customer = auth_customer()) {
            return $this->dbItems((int) $customer['id']);
        }

        return $this->sessionItems();
    }

    /** @return array<int, array<string, mixed>> */
    private function sessionItems(): array
    {
        return $_SESSION[self::SESSION_KEY] ?? [];
    }

    /** @return array<int, array<string, mixed>> */
    private function dbItems(int $userId): array
    {
        $cartId = $this->getDbCartId($userId);
        if ($cartId === null) {
            return [];
        }

        return $this->db->fetchAll(
            'SELECT product_id, variant_id, quantity, unit_price
             FROM cart_items WHERE cart_id = :cart_id',
            ['cart_id' => $cartId]
        );
    }

    /** @param array<int, array<string, mixed>> $items */
    private function hydrateItems(array $items): array
    {
        $hydrated = [];

        foreach ($items as $item) {
            $product = $this->db->fetch(
                'SELECT p.id, p.name, p.slug, p.stock, p.base_price, p.sale_price,
                        c.name AS category_name,
                        (SELECT image_path FROM product_images pi
                         WHERE pi.product_id = p.id AND pi.is_primary = 1 LIMIT 1) AS primary_image
                 FROM products p
                 INNER JOIN categories c ON c.id = p.category_id
                 WHERE p.id = :id AND p.is_active = 1 AND p.deleted_at IS NULL',
                ['id' => $item['product_id']]
            );

            if ($product === null) {
                continue;
            }

            $variantLabel = null;
            if (!empty($item['variant_id'])) {
                $variant = $this->db->fetch(
                    'SELECT color_name, price_adjustment, stock FROM product_variants WHERE id = :id',
                    ['id' => $item['variant_id']]
                );
                if ($variant) {
                    $variantLabel = $variant['color_name'];
                    $item['unit_price'] = (float) $item['unit_price'] + (float) $variant['price_adjustment'];
                }
            } else {
                $item['unit_price'] = effective_price($product);
            }

            $qty = (int) $item['quantity'];
            $hydrated[] = array_merge($product, [
                'variant_id' => $item['variant_id'] ?? null,
                'variant_label' => $variantLabel,
                'quantity' => $qty,
                'unit_price' => (float) $item['unit_price'],
                'line_total' => round((float) $item['unit_price'] * $qty, 2),
                'image_url' => product_image_url($product['primary_image'] ?? null),
            ]);
        }

        return $hydrated;
    }

    /** @return array<string, mixed> */
    private function getProductForCart(int $productId, ?int $variantId): array
    {
        $product = $this->db->fetch(
            'SELECT id, base_price, sale_price, stock FROM products
             WHERE id = :id AND is_active = 1 AND deleted_at IS NULL',
            ['id' => $productId]
        );

        if ($product === null) {
            throw new \RuntimeException('Product unavailable.');
        }

        $price = effective_price($product);

        if ($variantId) {
            $variant = $this->db->fetch(
                'SELECT id, price_adjustment, stock FROM product_variants
                 WHERE id = :id AND product_id = :product_id AND is_active = 1',
                ['id' => $variantId, 'product_id' => $productId]
            );

            if ($variant === null) {
                throw new \RuntimeException('Variant unavailable.');
            }

            $price += (float) $variant['price_adjustment'];
        }

        return ['unit_price' => $price];
    }

    private function getOrCreateDbCart(int $userId): int
    {
        $existing = $this->getDbCartId($userId);
        if ($existing !== null) {
            return $existing;
        }

        return $this->db->insert(
            'INSERT INTO carts (user_id, session_id) VALUES (:user_id, NULL)',
            ['user_id' => $userId]
        );
    }

    private function getDbCartId(int $userId): ?int
    {
        $row = $this->db->fetch(
            'SELECT id FROM carts WHERE user_id = :user_id ORDER BY id DESC LIMIT 1',
            ['user_id' => $userId]
        );

        return $row ? (int) $row['id'] : null;
    }
}
