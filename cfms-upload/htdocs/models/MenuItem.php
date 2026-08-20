<?php

declare(strict_types=1);

final class MenuItem extends BaseModel
{
    public function listActive(): array
    {
        return $this->fetchAll(
            'SELECT * FROM menu_items WHERE is_active = 1 ORDER BY category ASC, name ASC'
        );
    }

    public function find(int $id): ?array
    {
        return $this->fetch(
            'SELECT * FROM menu_items WHERE id = :id AND is_active = 1 LIMIT 1',
            ['id' => $id]
        ) ?: null;
    }

    public function findByIds(array $ids): array
    {
        if (empty($ids)) {
            return [];
        }
        $params = [];
        $marks = [];
        foreach (array_values($ids) as $i => $id) {
            $key = 'id' . $i;
            $marks[] = ':' . $key;
            $params[$key] = (int) $id;
        }
        return $this->fetchAll(
            'SELECT * FROM menu_items WHERE is_active = 1 AND id IN (' . implode(',', $marks) . ')',
            $params
        );
    }
}
