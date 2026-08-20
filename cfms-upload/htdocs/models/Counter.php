<?php

declare(strict_types=1);

final class Counter extends BaseModel
{
    public function next(string $name): int
    {
        $this->beginTransaction();
        $this->execute(
            'INSERT INTO counters (name, seq) VALUES (:name, 1)
             ON DUPLICATE KEY UPDATE seq = LAST_INSERT_ID(seq + 1)',
            ['name' => $name]
        );
        $seq = (int) mysqli_insert_id($this->db);
        if ($seq === 0) {
            $seq = 1;
        }
        $this->commit();
        return $seq;
    }
}
