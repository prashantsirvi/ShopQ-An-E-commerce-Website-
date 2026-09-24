<?php

declare(strict_types=1);

namespace Models;

use Core\Model;

/**
 * Category model — catalog navigation and home page strips.
 */
final class Category extends Model
{
    /** @return array<int, array<string, mixed>> */
    public function getActive(int $limit = 10): array
    {
        $limit = max(1, $limit);

        return $this->db->fetchAll(
            "SELECT id, name, slug, description, icon, image
             FROM categories
             WHERE is_active = 1
             ORDER BY sort_order ASC, name ASC
             LIMIT {$limit}"
        );
    }

    public function findBySlug(string $slug): ?array
    {
        return $this->db->fetch(
            'SELECT * FROM categories WHERE slug = :slug AND is_active = 1 LIMIT 1',
            ['slug' => $slug]
        );
    }

    /** @return array<int, array<string, mixed>> */
    public function adminAll(): array
    {
        return $this->db->fetchAll('SELECT * FROM categories ORDER BY sort_order ASC, name ASC');
    }

    public function adminFind(int $id): ?array
    {
        return $this->db->fetch('SELECT * FROM categories WHERE id = :id LIMIT 1', ['id' => $id]);
    }

    public function adminCreate(array $data): int
    {
        return $this->db->insert(
            'INSERT INTO categories (name, slug, description, is_active, sort_order) VALUES (:name, :slug, :description, :is_active, :sort_order)',
            $data
        );
    }

    public function adminUpdate(int $id, array $data): void
    {
        $data['id'] = $id;
        $this->db->execute(
            'UPDATE categories SET name = :name, slug = :slug, description = :description, is_active = :is_active, sort_order = :sort_order WHERE id = :id',
            $data
        );
    }
}
