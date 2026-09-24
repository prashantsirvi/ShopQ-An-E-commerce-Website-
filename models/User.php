<?php

declare(strict_types=1);

namespace Models;

use Core\Model;

/**
 * User model — authentication, registration, and role lookups.
 */
final class User extends Model
{
    public function findByEmail(string $email): ?array
    {
        return $this->db->fetch(
            'SELECT u.*, r.slug AS role_slug, r.name AS role_name
             FROM users u
             INNER JOIN roles r ON r.id = u.role_id
             WHERE u.email = :email
             LIMIT 1',
            ['email' => strtolower(trim($email))]
        );
    }

    public function findById(int $id): ?array
    {
        return $this->db->fetch(
            'SELECT u.*, r.slug AS role_slug, r.name AS role_name
             FROM users u
             INNER JOIN roles r ON r.id = u.role_id
             WHERE u.id = :id
             LIMIT 1',
            ['id' => $id]
        );
    }

    public function emailExists(string $email): bool
    {
        $result = $this->db->fetch(
            'SELECT id FROM users WHERE email = :email LIMIT 1',
            ['email' => strtolower(trim($email))]
        );

        return $result !== null;
    }

    public function createCustomer(array $data): int
    {
        return $this->db->insert(
            'INSERT INTO users (role_id, first_name, last_name, email, phone, password, is_active, email_verified_at)
             VALUES (:role_id, :first_name, :last_name, :email, :phone, :password, 1, NOW())',
            [
                'role_id' => 2,
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name'],
                'email' => strtolower(trim($data['email'])),
                'phone' => $data['phone'] ?? null,
                'password' => password_hash($data['password'], PASSWORD_DEFAULT),
            ]
        );
    }

    public function verifyPassword(array $user, string $password): bool
    {
        return password_verify($password, (string) $user['password']);
    }

    public function updateLastLogin(int $userId): void
    {
        $this->db->execute(
            'UPDATE users SET last_login_at = NOW() WHERE id = :id',
            ['id' => $userId]
        );
    }

    public function createWalletForUser(int $userId): void
    {
        $exists = $this->db->fetch(
            'SELECT id FROM wallets WHERE user_id = :user_id LIMIT 1',
            ['user_id' => $userId]
        );

        if ($exists !== null) {
            return;
        }

        $this->db->insert(
            'INSERT INTO wallets (user_id, balance) VALUES (:user_id, 0.00)',
            ['user_id' => $userId]
        );
    }

    /** @return array{items: array<int, array<string, mixed>>, pagination: array<string, int>} */
    public function listCustomers(string $query, int $page, int $perPage = 15): array
    {
        $params = ['role' => 'customer'];
        $where = "r.slug = :role";

        if ($query !== '') {
            $where .= ' AND (u.email LIKE :q OR u.first_name LIKE :q OR u.last_name LIKE :q)';
            $params['q'] = '%' . $query . '%';
        }

        $total = (int) $this->db->fetch(
            "SELECT COUNT(*) AS total FROM users u INNER JOIN roles r ON r.id = u.role_id WHERE {$where}",
            $params
        )['total'];

        $pagination = paginate($total, $page, $perPage);

        $items = $this->db->fetchAll(
            "SELECT u.*, r.name AS role_name,
                    (SELECT COUNT(*) FROM orders o WHERE o.user_id = u.id) AS orders_count
             FROM users u
             INNER JOIN roles r ON r.id = u.role_id
             WHERE {$where}
             ORDER BY u.created_at DESC
             LIMIT {$pagination['per_page']} OFFSET {$pagination['offset']}",
            $params
        );

        return ['items' => $items, 'pagination' => $pagination];
    }
}
