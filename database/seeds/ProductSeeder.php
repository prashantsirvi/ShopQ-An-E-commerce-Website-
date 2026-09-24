<?php

declare(strict_types=1);

/**
 * Seeds 50 products with images and color variants.
 */
final class ProductSeeder
{
    public function __construct(private readonly PDO $pdo)
    {
    }

    public function run(): void
    {
        $products = $this->productCatalog();

        $productStmt = $this->pdo->prepare(
            'INSERT INTO products (
                category_id, brand_id, name, slug, sku, short_description, description,
                base_price, sale_price, discount_percent, stock,
                is_featured, is_trending, is_bestseller, rating_avg, rating_count, view_count
            ) VALUES (
                :category_id, :brand_id, :name, :slug, :sku, :short_description, :description,
                :base_price, :sale_price, :discount_percent, :stock,
                :is_featured, :is_trending, :is_bestseller, :rating_avg, :rating_count, :view_count
            )'
        );

        $imageStmt = $this->pdo->prepare(
            'INSERT INTO product_images (product_id, image_path, alt_text, sort_order, is_primary)
             VALUES (:product_id, :image_path, :alt_text, :sort_order, :is_primary)'
        );

        $variantStmt = $this->pdo->prepare(
            'INSERT INTO product_variants (product_id, color_name, color_hex, sku_suffix, stock, price_adjustment)
             VALUES (:product_id, :color_name, :color_hex, :sku_suffix, :stock, :price_adjustment)'
        );

        $tagStmt = $this->pdo->prepare(
            'INSERT IGNORE INTO product_tag_map (product_id, tag_id) VALUES (:product_id, :tag_id)'
        );

        $flashStmt = $this->pdo->prepare(
            'INSERT INTO flash_sale_products (flash_sale_id, product_id, flash_price, flash_stock)
             VALUES (1, :product_id, :flash_price, :flash_stock)'
        );

        foreach ($products as $index => $product) {
            $salePrice = $product['sale_price'];
            $discount = 0;

            if ($salePrice !== null && $product['base_price'] > 0) {
                $discount = (int) round((($product['base_price'] - $salePrice) / $product['base_price']) * 100);
            }

            $productStmt->execute([
                'category_id' => $product['category_id'],
                'brand_id' => $product['brand_id'],
                'name' => $product['name'],
                'slug' => $product['slug'],
                'sku' => $product['sku'],
                'short_description' => $product['short_description'],
                'description' => $product['description'],
                'base_price' => $product['base_price'],
                'sale_price' => $salePrice,
                'discount_percent' => $discount,
                'stock' => $product['stock'],
                'is_featured' => $product['is_featured'],
                'is_trending' => $product['is_trending'],
                'is_bestseller' => $product['is_bestseller'],
                'rating_avg' => $product['rating_avg'],
                'rating_count' => $product['rating_count'],
                'view_count' => $product['view_count'],
            ]);

            $productId = (int) $this->pdo->lastInsertId();

            foreach ($product['images'] as $imageIndex => $imagePath) {
                $imageStmt->execute([
                    'product_id' => $productId,
                    'image_path' => $imagePath,
                    'alt_text' => $product['name'],
                    'sort_order' => $imageIndex,
                    'is_primary' => $imageIndex === 0 ? 1 : 0,
                ]);
            }

            foreach ($product['variants'] as $variant) {
                $variantStmt->execute([
                    'product_id' => $productId,
                    'color_name' => $variant['color_name'],
                    'color_hex' => $variant['color_hex'],
                    'sku_suffix' => $variant['sku_suffix'],
                    'stock' => $variant['stock'],
                    'price_adjustment' => $variant['price_adjustment'],
                ]);
            }

            foreach ($product['tags'] as $tagId) {
                $tagStmt->execute([
                    'product_id' => $productId,
                    'tag_id' => $tagId,
                ]);
            }

            if ($index < 8) {
                $flashPrice = ($salePrice ?? $product['base_price']) * 0.9;
                $flashStmt->execute([
                    'product_id' => $productId,
                    'flash_price' => round($flashPrice, 2),
                    'flash_stock' => max(5, (int) ($product['stock'] / 4)),
                ]);
            }
        }
    }

    /** @return array<int, array<string, mixed>> */
    private function productCatalog(): array
    {
        $items = [
            ['Wireless Earbuds Pro', 'electronics', 1, 1, 2999, 2499, ['Black', 'White', 'Blue']],
            ['Smartwatch Series X', 'electronics', 1, 1, 4999, 4299, ['Black', 'Silver']],
            ['Bluetooth Speaker Mini', 'electronics', 1, 1, 1999, 1499, ['Red', 'Black']],
            ['4K Action Camera', 'electronics', 1, 1, 8999, 7499, ['Black']],
            ['USB-C Laptop Charger 65W', 'electronics', 1, 1, 2499, 1999, ['White', 'Black']],
            ['Men Slim Fit Casual Shirt', 'fashion', 2, 2, 1299, 999, ['White', 'Navy', 'Olive']],
            ['Women Floral Maxi Dress', 'fashion', 2, 2, 1899, 1599, ['Red', 'Blue', 'Yellow']],
            ['Denim Jacket Classic', 'fashion', 2, 2, 2499, 2199, ['Blue', 'Black']],
            ['Cotton Track Pants', 'fashion', 2, 2, 999, 799, ['Grey', 'Black', 'Navy']],
            ['Formal Blazer Slim', 'fashion', 2, 2, 3499, 2999, ['Black', 'Charcoal']],
            ['Non-Stick Cookware Set 5pc', 'home-kitchen', 3, 3, 2999, 2599, ['Black', 'Red']],
            ['Electric Kettle 1.8L', 'home-kitchen', 3, 3, 1499, 1199, ['Silver', 'Black']],
            ['Vacuum Storage Bags Pack', 'home-kitchen', 3, 3, 699, 549, ['Clear']],
            ['LED Desk Lamp Adjustable', 'home-kitchen', 3, 3, 1299, 999, ['White', 'Black']],
            ['Stainless Steel Lunch Box', 'home-kitchen', 3, 3, 899, 749, ['Steel', 'Black']],
            ['Atomic Habits — Paperback', 'books', 4, 4, 399, 349, ['Default']],
            ['Deep Work — Hardcover', 'books', 4, 4, 599, null, ['Default']],
            ['Indian Economy Basics', 'books', 4, 4, 450, 399, ['Default']],
            ['Children Story Collection', 'books', 4, 4, 299, 249, ['Default']],
            ['Programming with PHP 8', 'books', 4, 4, 799, 699, ['Default']],
            ['Yoga Mat 6mm Anti-Slip', 'sports-fitness', 5, 5, 899, 749, ['Purple', 'Blue', 'Black']],
            ['Adjustable Dumbbell Pair 5kg', 'sports-fitness', 5, 5, 2499, 2199, ['Black']],
            ['Running Shoes Lite', 'sports-fitness', 5, 5, 1999, 1699, ['Black', 'White', 'Red']],
            ['Resistance Bands Set', 'sports-fitness', 5, 5, 599, 499, ['Multi']],
            ['Sports Water Bottle 1L', 'sports-fitness', 5, 5, 399, 349, ['Blue', 'Black', 'Pink']],
            ['Vitamin C Face Serum', 'beauty', 6, 6, 699, 599, ['Default']],
            ['Matte Lipstick Set', 'beauty', 6, 6, 999, 849, ['Red', 'Pink', 'Nude']],
            ['Herbal Shampoo 400ml', 'beauty', 6, 6, 349, 299, ['Green']],
            ['Sunscreen SPF 50', 'beauty', 6, 6, 499, 449, ['White']],
            ['Beard Grooming Kit', 'beauty', 6, 6, 1299, 1099, ['Black']],
            ['Building Blocks 100pcs', 'toys-games', 7, 7, 899, 799, ['Multi']],
            ['Remote Control Racing Car', 'toys-games', 7, 7, 1499, 1299, ['Red', 'Blue']],
            ['Strategy Board Game', 'toys-games', 7, 7, 1299, 1099, ['Default']],
            ['Soft Toy Teddy Bear', 'toys-games', 7, 7, 599, 499, ['Brown', 'Cream']],
            ['Educational Puzzle Map', 'toys-games', 7, 7, 449, 399, ['Multi']],
            ['Organic Basmati Rice 5kg', 'grocery', 8, 8, 699, 649, ['Default']],
            ['Extra Virgin Olive Oil 1L', 'grocery', 8, 8, 899, 799, ['Default']],
            ['Green Tea Pack 100 bags', 'grocery', 8, 8, 399, 349, ['Default']],
            ['Mixed Dry Fruits 500g', 'grocery', 8, 8, 599, 549, ['Default']],
            ['Instant Coffee 200g', 'grocery', 8, 8, 349, 299, ['Default']],
            ['Casual Sneakers Urban', 'footwear', 9, 9, 2499, 2099, ['White', 'Black', 'Grey']],
            ['Leather Formal Shoes', 'footwear', 9, 9, 2999, 2599, ['Brown', 'Black']],
            ['Sports Sandals', 'footwear', 9, 9, 999, 849, ['Black', 'Blue']],
            ['Kids School Shoes', 'footwear', 9, 9, 1299, 1099, ['Black']],
            ['Hiking Boots Pro', 'footwear', 9, 9, 3499, 2999, ['Brown', 'Green']],
            ['Leather Wallet Classic', 'accessories', 10, 10, 799, 699, ['Brown', 'Black']],
            ['Analog Watch Minimal', 'accessories', 10, 10, 1999, 1699, ['Silver', 'Gold', 'Black']],
            ['Laptop Backpack 30L', 'accessories', 10, 10, 1499, 1299, ['Black', 'Grey', 'Navy']],
            ['Polarized Sunglasses', 'accessories', 10, 10, 999, 849, ['Black', 'Brown']],
            ['Canvas Belt Reversible', 'accessories', 10, 10, 499, 449, ['Brown', 'Black']],
        ];

        $colorMap = [
            'Black' => '#111111',
            'White' => '#FFFFFF',
            'Blue' => '#2563EB',
            'Red' => '#DC2626',
            'Silver' => '#C0C0C0',
            'Navy' => '#1E3A8A',
            'Olive' => '#556B2F',
            'Yellow' => '#EAB308',
            'Grey' => '#6B7280',
            'Charcoal' => '#374151',
            'Green' => '#16A34A',
            'Steel' => '#71717A',
            'Default' => '#9CA3AF',
            'Purple' => '#7C3AED',
            'Pink' => '#EC4899',
            'Nude' => '#D2B48C',
            'Multi' => '#6366F1',
            'Brown' => '#92400E',
            'Cream' => '#FFFDD0',
            'Gold' => '#D4AF37',
        ];

        $categoryMap = [
            'electronics' => 1,
            'fashion' => 2,
            'home-kitchen' => 3,
            'books' => 4,
            'sports-fitness' => 5,
            'beauty' => 6,
            'toys-games' => 7,
            'grocery' => 8,
            'footwear' => 9,
            'accessories' => 10,
        ];

        $catalog = [];

        foreach ($items as $index => $item) {
            [$name, $categorySlug, $categoryId, $brandId, $basePrice, $salePrice, $colors] = $item;
            $slug = strtolower(preg_replace('/[^a-z0-9]+/i', '-', $name));
            $slug = trim($slug, '-');
            $sku = 'SQ-' . str_pad((string) ($index + 1), 4, '0', STR_PAD_LEFT);

            $variants = [];
            foreach ($colors as $colorIndex => $colorName) {
                $suffix = strtoupper(preg_replace('/[^A-Z0-9]/', '', $colorName));
                if ($suffix === '') {
                    $suffix = 'VAR' . ($colorIndex + 1);
                }
                $suffix = substr($suffix, 0, 6) . ($colorIndex + 1);

                $variants[] = [
                    'color_name' => $colorName,
                    'color_hex' => $colorMap[$colorName] ?? '#9CA3AF',
                    'sku_suffix' => $suffix,
                    'stock' => random_int(8, 40),
                    'price_adjustment' => $colorIndex === 0 ? 0.00 : random_int(0, 1) * 100,
                ];
            }

            $images = [];
            for ($imageIndex = 1; $imageIndex <= 3; $imageIndex++) {
                $images[] = sprintf('uploads/products/%s-%d.jpg', $slug, $imageIndex);
            }

            $tags = [];
            if ($index % 5 === 0) {
                $tags[] = 1;
            }
            if ($index % 4 === 0) {
                $tags[] = 2;
            }
            if ($index % 3 === 0) {
                $tags[] = 3;
            }

            $catalog[] = [
                'category_id' => $categoryId,
                'brand_id' => $brandId,
                'name' => $name,
                'slug' => $slug,
                'sku' => $sku,
                'short_description' => 'Premium quality ' . strtolower($name) . ' with fast delivery.',
                'description' => $name . ' is designed for everyday use with durable materials, thoughtful design, and excellent value. Ideal for ShopQ customers looking for reliable quality and modern style.',
                'base_price' => $basePrice,
                'sale_price' => $salePrice,
                'stock' => array_sum(array_column($variants, 'stock')),
                'is_featured' => $index < 12 ? 1 : 0,
                'is_trending' => $index % 6 === 0 ? 1 : 0,
                'is_bestseller' => $index % 5 === 0 ? 1 : 0,
                'rating_avg' => round(random_int(35, 50) / 10, 1),
                'rating_count' => random_int(10, 500),
                'view_count' => random_int(100, 5000),
                'images' => $images,
                'variants' => $variants,
                'tags' => $tags,
            ];
        }

        return $catalog;
    }
}
