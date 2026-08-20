<?php

declare(strict_types=1);

final class User extends BaseModel
{
    public function findByEmail(string $email): ?array
    {
        return $this->fetch(
            'SELECT u.*, r.slug AS role_slug, r.name AS role_name
             FROM users u JOIN roles r ON r.id = u.role_id
             WHERE u.email = :email LIMIT 1',
            ['email' => strtolower(trim($email))]
        );
    }

    public function findById(int $id): ?array
    {
        return $this->fetch(
            'SELECT u.*, r.slug AS role_slug, r.name AS role_name
             FROM users u JOIN roles r ON r.id = u.role_id
             WHERE u.id = :id LIMIT 1',
            ['id' => $id]
        );
    }

    public function findByRememberTokenHash(?string $hash): ?array
    {
        if (!$hash) {
            return null;
        }
        return $this->fetch(
            'SELECT u.*, r.slug AS role_slug, r.name AS role_name
             FROM users u JOIN roles r ON r.id = u.role_id
             WHERE u.remember_token = :token LIMIT 1',
            ['token' => $hash]
        );
    }

    public function count(): int
    {
        return (int) $this->fetchColumn('SELECT COUNT(*) FROM users');
    }

    public function list(array $filters = []): array
    {
        $sql = 'SELECT u.*, r.slug AS role_slug, r.name AS role_name
                FROM users u JOIN roles r ON r.id = u.role_id WHERE 1=1';
        $params = [];
        if (!empty($filters['role'])) {
            $sql .= ' AND r.slug = :role';
            $params['role'] = $filters['role'] === 'admin' ? 'admin' : 'staff';
        }
        if (!empty($filters['search'])) {
            $sql .= ' AND (u.name LIKE :search OR u.email LIKE :search)';
            $params['search'] = '%' . $filters['search'] . '%';
        }
        $sql .= ' ORDER BY u.name ASC, u.email ASC';
        return $this->fetchAll($sql, $params);
    }

    public function findStaffByCategory(string $category): ?string
    {
        $email = $this->fetchColumn(
            'SELECT u.email FROM users u JOIN roles r ON r.id = u.role_id
             WHERE r.slug = "staff" AND u.is_active = 1 AND u.category = :category
             ORDER BY u.id ASC LIMIT 1',
            ['category' => $category]
        );
        return $email ?: null;
    }

    public function listWithAssignments(array $filters = []): array
    {
        $users = $this->list($filters);
        $stats = $this->fetchAll(
            'SELECT assigned_to,
                    COUNT(*) AS total,
                    SUM(status = "pending") AS pending,
                    SUM(status = "in-progress") AS in_progress,
                    SUM(status = "resolved") AS resolved,
                    SUM(status = "escalated") AS escalated
             FROM feedback
             WHERE assigned_to IS NOT NULL AND assigned_to <> ""
             GROUP BY assigned_to'
        );
        $map = [];
        foreach ($stats as $s) {
            $map[$s['assigned_to']] = $s;
        }
        foreach ($users as &$u) {
            $s = $map[$u['email']] ?? null;
            $u['assignmentStats'] = $s ? [
                'total' => (int) $s['total'],
                'pending' => (int) $s['pending'],
                'inProgress' => (int) $s['in_progress'],
                'resolved' => (int) $s['resolved'],
                'escalated' => (int) $s['escalated'],
            ] : ['total' => 0, 'pending' => 0, 'inProgress' => 0, 'resolved' => 0, 'escalated' => 0];
        }
        unset($u);
        return $users;
    }

    public function create(array $data): array
    {
        $roleSlug = ($data['role'] ?? 'staff') === 'admin' ? 'admin' : 'staff';
        $roleId = $this->roleId($roleSlug);
        $category = $roleSlug === 'staff' && !empty($data['category']) ? trim((string) $data['category']) : null;
        $id = $this->insert(
            'INSERT INTO users (email, name, role_id, password_hash, category, is_active, created_at, updated_at)
             VALUES (:email, :name, :role_id, :password_hash, :category, 1, :created_at, :updated_at)',
            [
                'email' => strtolower(trim((string) $data['email'])),
                'name' => trim((string) ($data['name'] ?? '')) ?: null,
                'role_id' => $roleId,
                'password_hash' => password_hash((string) $data['password'], PASSWORD_DEFAULT),
                'category' => $category,
                'created_at' => $this->now(),
                'updated_at' => $this->now(),
            ]
        );
        return $this->findById($id);
    }

    public function update(int $id, array $data): void
    {
        $fields = [];
        $params = ['id' => $id, 'updated_at' => $this->now()];
        foreach (['name', 'email', 'avatar_url'] as $field) {
            if (array_key_exists($field, $data)) {
                $fields[] = $field . ' = :' . $field;
                $params[$field] = $data[$field] ?: null;
            }
        }
        if (array_key_exists('is_active', $data)) {
            $fields[] = 'is_active = :is_active';
            $params['is_active'] = (int) $data['is_active'];
        }
        if (!empty($data['role'])) {
            $fields[] = 'role_id = :role_id';
            $params['role_id'] = $this->roleId($data['role']);
        }
        if (!empty($data['password'])) {
            $fields[] = 'password_hash = :password_hash';
            $params['password_hash'] = password_hash($data['password'], PASSWORD_DEFAULT);
        }
        if (array_key_exists('category', $data)) {
            $newRole = !empty($data['role']) ? $data['role'] : $this->fetchColumn('SELECT slug FROM roles WHERE id = (SELECT role_id FROM users WHERE id = :uid)', ['uid' => $id]);
            $fields[] = 'category = :category';
            $params['category'] = $newRole === 'staff' ? ($data['category'] ?: null) : null;
        }
        if (!$fields) {
            return;
        }
        $fields[] = 'updated_at = :updated_at';
        $this->execute('UPDATE users SET ' . implode(', ', $fields) . ' WHERE id = :id', $params);
    }

    public function delete(int $id): void
    {
        $this->execute(
            'UPDATE users SET is_active = 0, updated_at = :updated_at WHERE id = :id',
            ['id' => $id, 'updated_at' => $this->now()]
        );
    }

    public function storeRememberToken(int $id, ?string $hash): void
    {
        $this->execute('UPDATE users SET remember_token = :token WHERE id = :id', ['token' => $hash, 'id' => $id]);
    }

    public function serialize(array $user): array
    {
        return [
            'id' => (string) $user['id'],
            'email' => $user['email'],
            'role' => $user['role_slug'],
            'name' => $user['name'] ?? null,
            'avatarUrl' => $user['avatar_url'] ?? null,
        ];
    }

    private function roleId(string $slug): int
    {
        return (int) $this->fetchColumn('SELECT id FROM roles WHERE slug = :slug LIMIT 1', ['slug' => $slug]);
    }
}
