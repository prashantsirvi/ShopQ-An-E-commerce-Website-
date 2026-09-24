<?php

declare(strict_types=1);

namespace Models;

use Core\Model;

final class Banner extends Model
{
    /** @return array<int, array<string, mixed>> */
    public function adminAll(): array
    {
        return $this->db->fetchAll('SELECT * FROM banners ORDER BY sort_order ASC, id DESC');
    }

    public function adminFind(int $id): ?array
    {
        return $this->db->fetch('SELECT * FROM banners WHERE id = :id LIMIT 1', ['id' => $id]);
    }

    public function adminCreate(array $data): int
    {
        return $this->db->insert(
            'INSERT INTO banners (title, subtitle, image_path, link_url, placement, sort_order, is_active)
             VALUES (:title, :subtitle, :image_path, :link_url, :placement, :sort_order, :is_active)',
            $data
        );
    }

    public function adminUpdate(int $id, array $data): void
    {
        $data['id'] = $id;
        $this->db->execute(
            'UPDATE banners SET title = :title, subtitle = :subtitle, image_path = :image_path, link_url = :link_url,
             placement = :placement, sort_order = :sort_order, is_active = :is_active WHERE id = :id',
            $data
        );
    }

    public function adminDelete(int $id): void
    {
        $this->db->execute('DELETE FROM banners WHERE id = :id', ['id' => $id]);
    }
}
