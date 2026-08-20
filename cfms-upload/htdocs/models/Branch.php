<?php

declare(strict_types=1);

final class Branch extends BaseModel
{
    public function list(array $filters = []): array
    {
        $sql = 'SELECT * FROM branches WHERE is_active = 1';
        $params = [];
        if (!empty($filters['search'])) {
            $sql .= ' AND name LIKE :search';
            $params['search'] = '%' . $filters['search'] . '%';
        }
        $sql .= ' ORDER BY name ASC';
        return $this->fetchAll($sql, $params);
    }

    public function find(int $id): ?array
    {
        return $this->fetch('SELECT * FROM branches WHERE id = :id LIMIT 1', ['id' => $id]) ?: null;
    }

    public function byName(string $name): ?array
    {
        return $this->fetch('SELECT * FROM branches WHERE name = :name LIMIT 1', ['name' => $name]) ?: null;
    }

    public function create(array $data): array
    {
        $id = $this->insert(
            'INSERT INTO branches (name, is_active, created_at, updated_at)
             VALUES (:name, 1, :created_at, :updated_at)',
            [
                'name' => trim((string) $data['name']),
                'created_at' => $this->now(),
                'updated_at' => $this->now(),
            ]
        );
        return $this->find($id);
    }

    public function counts(): array
    {
        return $this->fetchAll(
            'SELECT b.id, b.name, COUNT(f.id) AS total
             FROM branches b LEFT JOIN feedback f ON f.branch_id = b.id
             GROUP BY b.id, b.name ORDER BY b.name ASC'
        );
    }
}
