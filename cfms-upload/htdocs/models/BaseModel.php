<?php

declare(strict_types=1);

abstract class BaseModel
{
    protected mysqli $db;

    public function __construct()
    {
        $this->db = db();
    }

    protected function now(): string
    {
        return date('Y-m-d H:i:s');
    }

    // Run a SELECT and return the first row (or null)
    protected function fetch(string $sql, array $params = []): ?array
    {
        $stmt = $this->prepare($sql, $params);
        if (!$stmt) return null;
        $result = mysqli_stmt_get_result($stmt);
        $row = mysqli_fetch_assoc($result);
        mysqli_free_result($result);
        mysqli_stmt_close($stmt);
        return $row ?: null;
    }

    // Run a SELECT and return ALL rows
    protected function fetchAll(string $sql, array $params = []): array
    {
        $stmt = $this->prepare($sql, $params);
        if (!$stmt) return [];
        $result = mysqli_stmt_get_result($stmt);
        $rows = mysqli_fetch_all($result, MYSQLI_ASSOC);
        mysqli_free_result($result);
        mysqli_stmt_close($stmt);
        return $rows;
    }

    // Run a SELECT and return the first column of the first row
    protected function fetchColumn(string $sql, array $params = []): mixed
    {
        $stmt = $this->prepare($sql, $params);
        if (!$stmt) return null;
        $result = mysqli_stmt_get_result($stmt);
        $row = mysqli_fetch_row($result);
        mysqli_free_result($result);
        mysqli_stmt_close($stmt);
        return $row[0] ?? null;
    }

    // Run a plain query (no params) and return the result object
    protected function query(string $sql): mysqli_result|bool
    {
        return mysqli_query($this->db, $sql);
    }

    // Run INSERT and return the new row ID
    protected function insert(string $sql, array $params = []): int
    {
        $stmt = $this->prepare($sql, $params);
        if (!$stmt) return 0;
        mysqli_stmt_close($stmt);
        return mysqli_insert_id($this->db);
    }

    // Run UPDATE/DELETE and return number of affected rows
    protected function execute(string $sql, array $params = []): int
    {
        $stmt = $this->prepare($sql, $params);
        if (!$stmt) return 0;
        $affected = mysqli_stmt_affected_rows($stmt);
        mysqli_stmt_close($stmt);
        return $affected;
    }

    // Prepare a statement with named parameters (:name)
    // Converts :name to ? and binds values automatically
    private function prepare(string $sql, array $params = []): mysqli_stmt|false
    {
        // Find all :name placeholders
        preg_match_all('/:(\w+)/', $sql, $matches);
        $names = $matches[1];

        // Replace :name with ?
        $sql = preg_replace('/:(\w+)/', '?', $sql);

        $stmt = mysqli_prepare($this->db, $sql);
        if (!$stmt) return false;

        // Bind params in order
        if (!empty($names)) {
            $types = '';
            $values = [];
            foreach ($names as $name) {
                $v = $params[$name] ?? null;
                if (is_int($v)) {
                    $types .= 'i';
                } elseif (is_float($v)) {
                    $types .= 'd';
                } else {
                    $types .= 's';
                }
                $values[] = $v;
            }
            mysqli_stmt_bind_param($stmt, $types, ...$values);
        }

        mysqli_stmt_execute($stmt);
        return $stmt;
    }

    protected function beginTransaction(): void
    {
        mysqli_begin_transaction($this->db);
    }

    protected function commit(): void
    {
        mysqli_commit($this->db);
    }
}
