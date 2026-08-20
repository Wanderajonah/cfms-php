<?php

declare(strict_types=1);

final class ResponseTemplate extends BaseModel
{
    public function all(string $category = ''): array
    {
        $sql = 'SELECT rt.*, u.name AS created_by_name FROM response_templates rt LEFT JOIN users u ON u.id = rt.created_by';
        $params = [];
        if ($category !== '') {
            $sql .= ' WHERE rt.category = :category';
            $params['category'] = $category;
        }
        $sql .= ' ORDER BY rt.title ASC';
        return $this->fetchAll($sql, $params);
    }

    public function categories(): array
    {
        $rows = $this->fetchAll('SELECT DISTINCT category FROM response_templates WHERE category IS NOT NULL ORDER BY category ASC');
        return array_map(static fn ($r) => $r['category'], $rows);
    }

    public function create(array $data): array
    {
        $id = $this->insert(
            'INSERT INTO response_templates (title, body, category, created_by, created_at, updated_at)
             VALUES (:title, :body, :category, :created_by, :created_at, :updated_at)',
            [
                'title' => trim((string) $data['title']),
                'body' => trim((string) $data['body']),
                'category' => !empty($data['category']) ? trim((string) $data['category']) : null,
                'created_by' => Auth::id(),
                'created_at' => $this->now(),
                'updated_at' => $this->now(),
            ]
        );
        return $this->find($id);
    }

    public function find(int $id): ?array
    {
        return $this->fetch('SELECT * FROM response_templates WHERE id = :id', ['id' => $id]);
    }

    public function update(int $id, array $data): void
    {
        $fields = [];
        $params = ['id' => $id, 'updated_at' => $this->now()];
        foreach (['title', 'body', 'category'] as $field) {
            if (array_key_exists($field, $data)) {
                $fields[] = "$field = :$field";
                $params[$field] = $data[$field] !== '' ? trim((string) $data[$field]) : null;
            }
        }
        if (!$fields) return;
        $fields[] = 'updated_at = :updated_at';
        $this->execute(
            'UPDATE response_templates SET ' . implode(', ', $fields) . ' WHERE id = :id',
            $params
        );
    }

    public function delete(int $id): void
    {
        $this->execute('DELETE FROM response_templates WHERE id = :id', ['id' => $id]);
    }
}
