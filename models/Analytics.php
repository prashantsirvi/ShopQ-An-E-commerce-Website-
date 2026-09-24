<?php

declare(strict_types=1);

namespace Models;

use Core\Model;

final class Analytics extends Model
{
    public function dashboardSummary(): array
    {
        return [
            'orders_total' => (int) $this->db->fetch('SELECT COUNT(*) AS c FROM orders')['c'],
            'revenue_total' => (float) ($this->db->fetch(
                "SELECT COALESCE(SUM(total_amount), 0) AS total FROM orders WHERE payment_status IN ('paid','pending')"
            )['total'] ?? 0),
            'customers_total' => (int) $this->db->fetch(
                "SELECT COUNT(*) AS c FROM users u INNER JOIN roles r ON r.id = u.role_id WHERE r.slug = 'customer'"
            )['c'],
            'products_total' => (int) $this->db->fetch(
                'SELECT COUNT(*) AS c FROM products WHERE deleted_at IS NULL'
            )['c'],
            'pending_orders' => (int) $this->db->fetch(
                "SELECT COUNT(*) AS c FROM orders WHERE status IN ('pending','confirmed','processing')"
            )['c'],
            'low_stock' => (int) $this->db->fetch(
                'SELECT COUNT(*) AS c FROM products WHERE stock <= low_stock_threshold AND deleted_at IS NULL AND is_active = 1'
            )['c'],
        ];
    }

    /** @return array<int, array<string, mixed>> */
    public function revenueLastDays(int $days = 7): array
    {
        $days = max(1, min($days, 30));

        return $this->db->fetchAll(
            "SELECT DATE(placed_at) AS day, COUNT(*) AS orders_count, COALESCE(SUM(total_amount), 0) AS revenue
             FROM orders
             WHERE placed_at >= DATE_SUB(CURDATE(), INTERVAL :days DAY)
             GROUP BY DATE(placed_at)
             ORDER BY day ASC",
            ['days' => $days - 1]
        );
    }

    /** @return array<int, array<string, mixed>> */
    public function ordersByStatus(): array
    {
        return $this->db->fetchAll(
            'SELECT status, COUNT(*) AS total FROM orders GROUP BY status ORDER BY total DESC'
        );
    }

    /** @return array<int, array<string, mixed>> */
    public function topProducts(int $limit = 5): array
    {
        $limit = max(1, $limit);

        return $this->db->fetchAll(
            "SELECT oi.product_name, SUM(oi.quantity) AS qty_sold, SUM(oi.total_price) AS revenue
             FROM order_items oi
             GROUP BY oi.product_id, oi.product_name
             ORDER BY qty_sold DESC
             LIMIT {$limit}"
        );
    }

    /** @return array<int, array<string, mixed>> */
    public function popularSearches(int $limit = 8): array
    {
        $limit = max(1, $limit);

        return $this->db->fetchAll(
            "SELECT query, COUNT(*) AS searches
             FROM search_logs
             GROUP BY query
             ORDER BY searches DESC
             LIMIT {$limit}"
        );
    }

    /** @return array<int, array<string, mixed>> */
    public function trendingProducts(int $limit = 5): array
    {
        $limit = max(1, $limit);

        return $this->db->fetchAll(
            "SELECT p.id, p.name, p.slug, COUNT(pv.id) AS views
             FROM product_views pv
             INNER JOIN products p ON p.id = pv.product_id
             WHERE pv.viewed_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)
             GROUP BY p.id, p.name, p.slug
             ORDER BY views DESC
             LIMIT {$limit}"
        );
    }
}
