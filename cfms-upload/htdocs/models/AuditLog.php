<?php

declare(strict_types=1);

final class AuditLog extends BaseModel
{
    public function record(?int $userId, string $action, ?string $entityType = null, ?int $entityId = null, ?string $description = null): void
    {
        $this->execute(
            'INSERT INTO audit_logs (user_id, action, entity_type, entity_id, description, ip_address, user_agent, created_at)
             VALUES (:user_id, :action, :entity_type, :entity_id, :description, :ip_address, :user_agent, :created_at)',
            [
                'user_id' => $userId,
                'action' => $action,
                'entity_type' => $entityType,
                'entity_id' => $entityId,
                'description' => $description,
                'ip_address' => $_SERVER['REMOTE_ADDR'] ?? null,
                'user_agent' => substr($_SERVER['HTTP_USER_AGENT'] ?? '', 0, 255),
                'created_at' => $this->now(),
            ]
        );
    }

    public function latest(int $limit = 100): array
    {
        return $this->fetchAll(
            'SELECT a.*, u.name AS user_name, u.email AS user_email
             FROM audit_logs a LEFT JOIN users u ON u.id = a.user_id
             ORDER BY a.created_at DESC LIMIT :limit',
            ['limit' => $limit]
        );
    }

    public function forUser(int $userId, int $limit = 50): array
    {
        return $this->fetchAll(
            'SELECT a.*, u.name AS user_name, u.email AS user_email
             FROM audit_logs a LEFT JOIN users u ON u.id = a.user_id
             WHERE a.user_id = :user_id
             ORDER BY a.created_at DESC LIMIT :limit',
            ['user_id' => $userId, 'limit' => $limit]
        );
    }
}
