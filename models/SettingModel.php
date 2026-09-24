<?php

declare(strict_types=1);

namespace Models;

use Core\Model;

final class SettingModel extends Model
{
    /** @return array<int, array<string, mixed>> */
    public function all(): array
    {
        return $this->db->fetchAll('SELECT * FROM settings ORDER BY setting_group ASC, setting_key ASC');
    }

    public function updateValue(string $key, string $value): void
    {
        $this->db->execute(
            'UPDATE settings SET setting_value = :value WHERE setting_key = :key',
            ['value' => $value, 'key' => $key]
        );
    }
}
