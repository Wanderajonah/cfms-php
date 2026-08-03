<?php

declare(strict_types=1);

final class Order extends BaseModel
{
    public function create(array $data): int
    {
        return $this->insert(
            'INSERT INTO orders (
                order_number, customer_name, phone, email, branch_id, order_type,
                delivery_address, subtotal, delivery_fee, total, status, payment_method, payment_phone, notes, items_json, created_at
             ) VALUES (
                :order_number, :customer_name, :phone, :email, :branch_id, :order_type,
                :delivery_address, :subtotal, :delivery_fee, :total, :status, :payment_method, :payment_phone, :notes, :items_json, :created_at
             )',
            [
                'order_number' => (int) $data['order_number'],
                'customer_name' => trim((string) $data['customer_name']),
                'phone' => trim((string) $data['phone']),
                'email' => trim((string) ($data['email'] ?? '')) ?: null,
                'branch_id' => !empty($data['branch_id']) ? (int) $data['branch_id'] : null,
                'order_type' => (string) $data['order_type'],
                'delivery_address' => trim((string) ($data['delivery_address'] ?? '')) ?: null,
                'subtotal' => (int) $data['subtotal'],
                'delivery_fee' => (int) $data['delivery_fee'],
                'total' => (int) $data['total'],
                'status' => 'pending',
                'payment_method' => (string) $data['payment_method'],
                'payment_phone' => trim((string) ($data['payment_phone'] ?? '')) ?: null,
                'notes' => trim((string) ($data['notes'] ?? '')) ?: null,
                'items_json' => (string) $data['items_json'],
                'created_at' => $this->now(),
            ]
        );
    }

    public function find(int $id): ?array
    {
        return $this->fetch('SELECT * FROM orders WHERE id = :id LIMIT 1', ['id' => $id]) ?: null;
    }
}