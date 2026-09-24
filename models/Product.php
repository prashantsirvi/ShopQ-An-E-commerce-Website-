<?php

declare(strict_types=1);

namespace Models;

use Core\Model;

/**
 * Product model — catalog listing, search, detail, and smart queries.
 */
final class Product extends Model
{
    private const SORT_MAP = [
        'newest' => 'p.created_at DESC',
        'price_asc' => 'display_price ASC',
        'price_desc' => 'display_price DESC',
        'popular' => 'p.view_count DESC, p.rating_count DESC',
        'rating' => 'p.rating_avg DESC, p.rating_count DESC',
    ];

    /** @return array{items: array<int, array<string, mixed>>, pagination: array<string, int>} */
    public function list(array $filters, string $sort, int $page, int $perPage = 12): array
    {
        [$whereSql, $params] = $this->buildFilterClause($filters);
        $orderBy = self::SORT_MAP[$sort] ?? self::SORT_MAP['newest'];

        $countSql = "SELECT COUNT(*) AS total
                     FROM products p
                     WHERE {$whereSql}";

        $total = (int) $this->db->fetch($countSql, $params)['total'];
        $pagination = paginate($total, $page, $perPage);

        $sql = "SELECT p.*, c.name AS category_name, c.slug AS category_slug,
                       b.name AS brand_name, b.slug AS brand_slug,
                       COALESCE(p.sale_price, p.base_price) AS display_price,
                       (SELECT image_path FROM product_images pi
                        WHERE pi.product_id = p.id AND pi.is_primary = 1
                        ORDER BY pi.sort_order ASC LIMIT 1) AS primary_image
                FROM products p
                INNER JOIN categories c ON c.id = p.category_id
                LEFT JOIN brands b ON b.id = p.brand_id
                WHERE {$whereSql}
                ORDER BY {$orderBy}
                LIMIT {$pagination['per_page']} OFFSET {$pagination['offset']}";

        $items = $this->hydrateCollection($this->db->fetchAll($sql, $params));

        return ['items' => $items, 'pagination' => $pagination];
    }

    /** @return array{items: array<int, array<string, mixed>>, pagination: array<string, int>} */
    public function search(string $query, int $page, int $perPage = 12, string $sort = 'popular'): array
    {
        $query = trim($query);

        if ($query === '') {
            return ['items' => [], 'pagination' => paginate(0, 1, $perPage)];
        }

        $like = '%' . $query . '%';
        $params = [
            'like' => $like,
            'exact' => $query,
        ];

        $whereSql = 'p.is_active = 1 AND p.deleted_at IS NULL AND (
            p.name LIKE :like OR p.short_description LIKE :like OR p.sku LIKE :like OR p.description LIKE :like
        )';

        $total = (int) $this->db->fetch(
            "SELECT COUNT(*) AS total FROM products p WHERE {$whereSql}",
            $params
        )['total'];

        $this->logSearch($query, $total);

        $pagination = paginate($total, $page, $perPage);
        $orderBy = self::SORT_MAP[$sort] ?? self::SORT_MAP['popular'];

        $sql = "SELECT p.*, c.name AS category_name, c.slug AS category_slug,
                       b.name AS brand_name, b.slug AS brand_slug,
                       COALESCE(p.sale_price, p.base_price) AS display_price,
                       (SELECT image_path FROM product_images pi
                        WHERE pi.product_id = p.id AND pi.is_primary = 1
                        ORDER BY pi.sort_order ASC LIMIT 1) AS primary_image,
                       CASE
                           WHEN p.name LIKE :exact THEN 3
                           WHEN p.name LIKE :like THEN 2
                           ELSE 1
                       END AS relevance
                FROM products p
                INNER JOIN categories c ON c.id = p.category_id
                LEFT JOIN brands b ON b.id = p.brand_id
                WHERE {$whereSql}
                ORDER BY relevance DESC, {$orderBy}
                LIMIT {$pagination['per_page']} OFFSET {$pagination['offset']}";

        $items = $this->hydrateCollection($this->db->fetchAll($sql, $params));

        return ['items' => $items, 'pagination' => $pagination];
    }

    /** @return array<int, array<string, mixed>> */
    public function suggestions(string $query, int $limit = 6): array
    {
        $query = trim($query);

        if (strlen($query) < 2) {
            return [];
        }

        $limit = max(1, $limit);

        return $this->hydrateCollection(
            $this->db->fetchAll(
                "SELECT p.id, p.name, p.slug, p.base_price, p.sale_price,
                        (SELECT image_path FROM product_images pi
                         WHERE pi.product_id = p.id AND pi.is_primary = 1
                         ORDER BY pi.sort_order ASC LIMIT 1) AS primary_image
                 FROM products p
                 WHERE p.is_active = 1 AND p.deleted_at IS NULL
                   AND (p.name LIKE :like OR p.sku LIKE :like)
                 ORDER BY p.view_count DESC, p.name ASC
                 LIMIT {$limit}",
                ['like' => '%' . $query . '%']
            )
        );
    }

    public function findBySlug(string $slug): ?array
    {
        $product = $this->db->fetch(
            'SELECT p.*, c.name AS category_name, c.slug AS category_slug,
                    b.name AS brand_name, b.slug AS brand_slug
             FROM products p
             INNER JOIN categories c ON c.id = p.category_id
             LEFT JOIN brands b ON b.id = p.brand_id
             WHERE p.slug = :slug AND p.is_active = 1 AND p.deleted_at IS NULL
             LIMIT 1',
            ['slug' => $slug]
        );

        if ($product === null) {
            return null;
        }

        $product['display_price'] = effective_price($product);
        $product['images'] = $this->getImages((int) $product['id']);
        $product['variants'] = $this->getVariants((int) $product['id']);
        $product['image_url'] = product_image_url($product['images'][0]['image_path'] ?? null);

        return $product;
    }

    /** @return array<int, array<string, mixed>> */
    public function getImages(int $productId): array
    {
        return $this->db->fetchAll(
            'SELECT id, image_path, alt_text, sort_order, is_primary
             FROM product_images
             WHERE product_id = :product_id
             ORDER BY is_primary DESC, sort_order ASC',
            ['product_id' => $productId]
        );
    }

    /** @return array<int, array<string, mixed>> */
    public function getVariants(int $productId): array
    {
        return $this->db->fetchAll(
            'SELECT id, color_name, color_hex, sku_suffix, stock, price_adjustment
             FROM product_variants
             WHERE product_id = :product_id AND is_active = 1
             ORDER BY id ASC',
            ['product_id' => $productId]
        );
    }

    /** @return array<int, array<string, mixed>> */
    public function getRelated(int $productId, int $categoryId, int $limit = 4): array
    {
        $limit = max(1, $limit);

        return $this->hydrateCollection(
            $this->db->fetchAll(
                "SELECT p.*, c.name AS category_name, c.slug AS category_slug,
                        (SELECT image_path FROM product_images pi
                         WHERE pi.product_id = p.id AND pi.is_primary = 1
                         ORDER BY pi.sort_order ASC LIMIT 1) AS primary_image
                 FROM products p
                 INNER JOIN categories c ON c.id = p.category_id
                 WHERE p.category_id = :category_id
                   AND p.id != :product_id
                   AND p.is_active = 1 AND p.deleted_at IS NULL
                 ORDER BY p.is_featured DESC, p.rating_avg DESC
                 LIMIT {$limit}",
                ['category_id' => $categoryId, 'product_id' => $productId]
            )
        );
    }

    /** @return array<int, array<string, mixed>> */
    public function getRecentlyViewed(array $ids, int $limit = 4): array
    {
        $ids = array_values(array_unique(array_map('intval', $ids)));

        if ($ids === []) {
            return [];
        }

        $idList = implode(',', $ids);
        $limit = max(1, $limit);

        $rows = $this->db->fetchAll(
            "SELECT p.*, c.name AS category_name, c.slug AS category_slug,
                    (SELECT image_path FROM product_images pi
                     WHERE pi.product_id = p.id AND pi.is_primary = 1
                     ORDER BY pi.sort_order ASC LIMIT 1) AS primary_image
             FROM products p
             INNER JOIN categories c ON c.id = p.category_id
             WHERE p.id IN ({$idList}) AND p.is_active = 1 AND p.deleted_at IS NULL
             LIMIT {$limit}"
        );

        return $this->hydrateCollection($rows);
    }

    /** @return array{items: array<int, array<string, mixed>>, pagination: array<string, int>} */
    public function getDeals(int $page, int $perPage = 12): array
    {
        return $this->list(['deals_only' => true], 'price_desc', $page, $perPage);
    }

    /** @return array<int, string> */
    public function popularSearches(int $limit = 5): array
    {
        $limit = max(1, $limit);

        $rows = $this->db->fetchAll(
            "SELECT query, COUNT(*) AS total
             FROM search_logs
             GROUP BY query
             ORDER BY total DESC
             LIMIT {$limit}"
        );

        return array_column($rows, 'query');
    }

    public function recordView(int $productId): void
    {
        $this->db->execute(
            'UPDATE products SET view_count = view_count + 1 WHERE id = :id',
            ['id' => $productId]
        );

        $customer = auth_customer();

        $this->db->insert(
            'INSERT INTO product_views (product_id, user_id, session_id, ip_address)
             VALUES (:product_id, :user_id, :session_id, :ip_address)',
            [
                'product_id' => $productId,
                'user_id' => $customer['id'] ?? null,
                'session_id' => session_id(),
                'ip_address' => $_SERVER['REMOTE_ADDR'] ?? null,
            ]
        );
    }

    /** @return array{min: float, max: float} */
    public function priceRange(): array
    {
        $row = $this->db->fetch(
            'SELECT MIN(COALESCE(sale_price, base_price)) AS min_price,
                    MAX(COALESCE(sale_price, base_price)) AS max_price
             FROM products WHERE is_active = 1 AND deleted_at IS NULL'
        );

        return [
            'min' => (float) ($row['min_price'] ?? 0),
            'max' => (float) ($row['max_price'] ?? 0),
        ];
    }

    /** @return array{0: string, 1: array<string, mixed>} */
    private function buildFilterClause(array $filters): array
    {
        $where = ['p.is_active = 1', 'p.deleted_at IS NULL'];
        $params = [];

        if (!empty($filters['category_id'])) {
            $where[] = 'p.category_id = :category_id';
            $params['category_id'] = (int) $filters['category_id'];
        }

        if (!empty($filters['brand_id'])) {
            $where[] = 'p.brand_id = :brand_id';
            $params['brand_id'] = (int) $filters['brand_id'];
        }

        if (!empty($filters['min_price'])) {
            $where[] = 'COALESCE(p.sale_price, p.base_price) >= :min_price';
            $params['min_price'] = (float) $filters['min_price'];
        }

        if (!empty($filters['max_price'])) {
            $where[] = 'COALESCE(p.sale_price, p.base_price) <= :max_price';
            $params['max_price'] = (float) $filters['max_price'];
        }

        if (!empty($filters['min_rating'])) {
            $where[] = 'p.rating_avg >= :min_rating';
            $params['min_rating'] = (float) $filters['min_rating'];
        }

        if (!empty($filters['deals_only'])) {
            $where[] = '(p.discount_percent > 0 OR (p.sale_price IS NOT NULL AND p.sale_price < p.base_price))';
        }

        if (!empty($filters['in_stock'])) {
            $where[] = 'p.stock > 0';
        }

        return [implode(' AND ', $where), $params];
    }

    private function logSearch(string $query, int $resultsCount): void
    {
        $customer = auth_customer();

        $this->db->insert(
            'INSERT INTO search_logs (query, user_id, results_count)
             VALUES (:query, :user_id, :results_count)',
            [
                'query' => mb_substr($query, 0, 200),
                'user_id' => $customer['id'] ?? null,
                'results_count' => $resultsCount,
            ]
        );
    }

    /** @return array<int, array<string, mixed>> */
    public function getFeatured(int $limit = 8): array
    {
        return $this->hydrateCollection($this->baseSelect('p.is_featured = 1', 'p.created_at DESC', $limit));
    }

    /** @return array<int, array<string, mixed>> */
    public function getBestSellers(int $limit = 8): array
    {
        return $this->hydrateCollection($this->baseSelect('p.is_bestseller = 1', 'p.rating_count DESC, p.view_count DESC', $limit));
    }

    /** @return array<int, array<string, mixed>> */
    public function getTrending(int $limit = 8): array
    {
        return $this->hydrateCollection($this->baseSelect('p.is_trending = 1', 'p.view_count DESC, p.rating_avg DESC', $limit));
    }

    /** @return array<int, array<string, mixed>> */
    public function getFlashSale(int $limit = 6): array
    {
        $limit = max(1, $limit);

        $rows = $this->db->fetchAll(
            "SELECT p.*, c.name AS category_name, c.slug AS category_slug,
                    fsp.flash_price, fsp.flash_stock,
                    (SELECT image_path FROM product_images pi
                     WHERE pi.product_id = p.id AND pi.is_primary = 1
                     ORDER BY pi.sort_order ASC LIMIT 1) AS primary_image
             FROM flash_sale_products fsp
             INNER JOIN products p ON p.id = fsp.product_id
             INNER JOIN categories c ON c.id = p.category_id
             INNER JOIN flash_sales fs ON fs.id = fsp.flash_sale_id
             WHERE p.is_active = 1 AND p.deleted_at IS NULL AND fs.is_active = 1
               AND NOW() BETWEEN fs.starts_at AND fs.ends_at
             ORDER BY fsp.flash_price ASC
             LIMIT {$limit}"
        );

        return $this->hydrateCollection($rows, true);
    }

    /** @return array<int, array<string, mixed>> */
    private function baseSelect(string $condition, string $orderBy, int $limit): array
    {
        $limit = max(1, $limit);

        return $this->db->fetchAll(
            "SELECT p.*, c.name AS category_name, c.slug AS category_slug,
                    (SELECT image_path FROM product_images pi
                     WHERE pi.product_id = p.id AND pi.is_primary = 1
                     ORDER BY pi.sort_order ASC LIMIT 1) AS primary_image
             FROM products p
             INNER JOIN categories c ON c.id = p.category_id
             WHERE p.is_active = 1 AND p.deleted_at IS NULL AND {$condition}
             ORDER BY {$orderBy}
             LIMIT {$limit}"
        );
    }

    /** @param array<int, array<string, mixed>> $products */
    private function hydrateCollection(array $products, bool $useFlashPrice = false): array
    {
        foreach ($products as &$product) {
            if ($useFlashPrice && isset($product['flash_price'])) {
                $product['display_price'] = (float) $product['flash_price'];
            } elseif (!isset($product['display_price'])) {
                $product['display_price'] = effective_price($product);
            }

            $product['image_url'] = product_image_url($product['primary_image'] ?? null);
        }

        return $products;
    }

    /** @return array{items: array<int, array<string, mixed>>, pagination: array<string, int>} */
    public function adminList(string $query, int $page, int $perPage = 15): array
    {
        $params = [];
        $where = 'p.deleted_at IS NULL';

        if ($query !== '') {
            $where .= ' AND (p.name LIKE :q OR p.sku LIKE :q)';
            $params['q'] = '%' . $query . '%';
        }

        $total = (int) $this->db->fetch("SELECT COUNT(*) AS total FROM products p WHERE {$where}", $params)['total'];
        $pagination = paginate($total, $page, $perPage);

        $items = $this->db->fetchAll(
            "SELECT p.*, c.name AS category_name
             FROM products p
             LEFT JOIN categories c ON c.id = p.category_id
             WHERE {$where}
             ORDER BY p.id DESC
             LIMIT {$pagination['per_page']} OFFSET {$pagination['offset']}",
            $params
        );

        return ['items' => $items, 'pagination' => $pagination];
    }

    public function adminFind(int $id): ?array
    {
        return $this->db->fetch('SELECT * FROM products WHERE id = :id AND deleted_at IS NULL LIMIT 1', ['id' => $id]);
    }

    public function adminCreate(array $data): int
    {
        return $this->db->insert(
            'INSERT INTO products (category_id, brand_id, name, slug, sku, short_description, description, base_price, sale_price, discount_percent, stock, is_active, is_featured)
             VALUES (:category_id, :brand_id, :name, :slug, :sku, :short_description, :description, :base_price, :sale_price, :discount_percent, :stock, :is_active, :is_featured)',
            $data
        );
    }

    public function adminUpdate(int $id, array $data): void
    {
        $data['id'] = $id;
        $this->db->execute(
            'UPDATE products SET category_id = :category_id, brand_id = :brand_id, name = :name, slug = :slug, sku = :sku,
             short_description = :short_description, description = :description, base_price = :base_price, sale_price = :sale_price,
             discount_percent = :discount_percent, stock = :stock, is_active = :is_active, is_featured = :is_featured
             WHERE id = :id AND deleted_at IS NULL',
            $data
        );
    }

    public function adminDelete(int $id): void
    {
        $this->db->execute('UPDATE products SET deleted_at = NOW(), is_active = 0 WHERE id = :id', ['id' => $id]);
    }
}
