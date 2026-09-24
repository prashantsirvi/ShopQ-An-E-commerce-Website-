<?php

declare(strict_types=1);

namespace Models;

use Core\Model;

final class Review extends Model
{
    /** @return array{items: array<int, array<string, mixed>>, pagination: array<string, int>} */
    public function adminList(bool $pendingOnly, int $page, int $perPage = 15): array
    {
        $where = $pendingOnly ? 'r.is_approved = 0' : '1=1';
        $total = (int) $this->db->fetch("SELECT COUNT(*) AS total FROM reviews r WHERE {$where}")['total'];
        $pagination = paginate($total, $page, $perPage);

        $items = $this->db->fetchAll(
            "SELECT r.*, p.name AS product_name, u.first_name, u.last_name, u.email
             FROM reviews r
             INNER JOIN products p ON p.id = r.product_id
             INNER JOIN users u ON u.id = r.user_id
             WHERE {$where}
             ORDER BY r.created_at DESC
             LIMIT {$pagination['per_page']} OFFSET {$pagination['offset']}"
        );

        return ['items' => $items, 'pagination' => $pagination];
    }

    public function setApproved(int $id, bool $approved): void
    {
        $this->db->execute('UPDATE reviews SET is_approved = :approved WHERE id = :id', [
            'approved' => $approved ? 1 : 0,
            'id' => $id,
        ]);

        if ($approved) {
            $this->refreshProductRating($id);
        }
    }

    private function refreshProductRating(int $reviewId): void
    {
        $review = $this->db->fetch('SELECT product_id FROM reviews WHERE id = :id', ['id' => $reviewId]);

        if ($review === null) {
            return;
        }

        $stats = $this->db->fetch(
            'SELECT AVG(rating) AS avg_rating, COUNT(*) AS total FROM reviews WHERE product_id = :product_id AND is_approved = 1',
            ['product_id' => $review['product_id']]
        );

        $this->db->execute(
            'UPDATE products SET rating_avg = :avg, rating_count = :count WHERE id = :id',
            [
                'avg' => round((float) ($stats['avg_rating'] ?? 0), 2),
                'count' => (int) ($stats['total'] ?? 0),
                'id' => $review['product_id'],
            ]
        );
    }
}
