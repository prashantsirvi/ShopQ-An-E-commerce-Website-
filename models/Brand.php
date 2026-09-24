<?php

declare(strict_types=1);

namespace Models;

use Core\Model;

final class Brand extends Model
{
    /** @return array<int, array<string, mixed>> */
    public function getActive(): array
    {
        return $this->db->fetchAll(
            'SELECT id, name, slug FROM brands WHERE is_active = 1 ORDER BY name ASC'
        );
    }

    public function findBySlug(string $slug): ?array
    {
        return $this->db->fetch(
            'SELECT id, name, slug FROM brands WHERE slug = :slug AND is_active = 1 LIMIT 1',
            ['slug' => $slug]
        );
    }
}
