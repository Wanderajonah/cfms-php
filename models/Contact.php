<?php

declare(strict_types=1);

final class Contact extends BaseModel
{
    public function list(string $search = ''): array
    {
        $sql = 'SELECT * FROM contacts WHERE is_active = 1';
        $params = [];
        if ($search !== '') {
            $sql .= ' AND (name LIKE :search OR phone LIKE :search OR email LIKE :search)';
            $params['search'] = '%' . $search . '%';
        }
        $sql .= ' ORDER BY name ASC LIMIT 200';
        return $this->fetchAll($sql, $params);
    }

    public function find(int $id): ?array
    {
        return $this->fetch('SELECT * FROM contacts WHERE id = :id LIMIT 1', ['id' => $id]);
    }

    public function create(array $data): array
    {
        if (empty($data['name']) || empty($data['phone'])) {
            throw new InvalidArgumentException('Name and phone are required');
        }
        $id = $this->insert(
            'INSERT INTO contacts (name, phone, email, notes, is_active, created_at, updated_at)
             VALUES (:name, :phone, :email, :notes, 1, :created_at, :updated_at)',
            [
                'name' => trim((string) $data['name']),
                'phone' => trim((string) $data['phone']),
                'email' => strtolower(trim((string) ($data['email'] ?? ''))) ?: null,
                'notes' => trim((string) ($data['notes'] ?? '')) ?: null,
                'created_at' => $this->now(),
                'updated_at' => $this->now(),
            ]
        );
        return $this->find($id);
    }

    public function deactivate(int $id): bool
    {
        $affected = $this->execute(
            'UPDATE contacts SET is_active = 0, updated_at = :updated_at WHERE id = :id',
            ['id' => $id, 'updated_at' => $this->now()]
        );
        return $affected > 0;
    }
}
