<?php

declare(strict_types=1);

final class Setting extends BaseModel
{
    public function all(): array
    {
        $rows = $this->fetchAll('SELECT setting_key, setting_value FROM settings ORDER BY setting_key');
        return array_column($rows, 'setting_value', 'setting_key');
    }

    public function save(array $settings): void
    {
        foreach ($settings as $key => $value) {
            $this->execute(
                'INSERT INTO settings (setting_key, setting_value, updated_at)
                 VALUES (:setting_key, :setting_value, :updated_at)
                 ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value), updated_at = VALUES(updated_at)',
                ['setting_key' => $key, 'setting_value' => (string) $value, 'updated_at' => $this->now()]
            );
        }
    }
}
