<?php

declare(strict_types=1);

namespace Models;

use Core\Model;

final class Address extends Model
{
    /** @return array<int, array<string, mixed>> */
    public function forUser(int $userId): array
    {
        return $this->db->fetchAll(
            'SELECT * FROM addresses WHERE user_id = :user_id ORDER BY is_default DESC, id DESC',
            ['user_id' => $userId]
        );
    }

    public function findForUser(int $addressId, int $userId): ?array
    {
        return $this->db->fetch(
            'SELECT * FROM addresses WHERE id = :id AND user_id = :user_id LIMIT 1',
            ['id' => $addressId, 'user_id' => $userId]
        );
    }

    public function create(int $userId, array $data): int
    {
        if (!empty($data['is_default'])) {
            $this->db->execute('UPDATE addresses SET is_default = 0 WHERE user_id = :user_id', ['user_id' => $userId]);
        }

        return $this->db->insert(
            'INSERT INTO addresses (user_id, label, full_name, phone, address_line1, address_line2, city, state, postal_code, country, is_default)
             VALUES (:user_id, :label, :full_name, :phone, :address_line1, :address_line2, :city, :state, :postal_code, :country, :is_default)',
            [
                'user_id' => $userId,
                'label' => $data['label'] ?? 'Home',
                'full_name' => $data['full_name'],
                'phone' => $data['phone'],
                'address_line1' => $data['address_line1'],
                'address_line2' => $data['address_line2'] ?? null,
                'city' => $data['city'],
                'state' => $data['state'],
                'postal_code' => $data['postal_code'],
                'country' => $data['country'] ?? 'India',
                'is_default' => !empty($data['is_default']) ? 1 : 0,
            ]
        );
    }
}
