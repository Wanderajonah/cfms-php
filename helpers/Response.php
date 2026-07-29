<?php

declare(strict_types=1);

final class Response
{
    public static function json(mixed $data, int $status = 200): void
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_SLASHES);
        exit;
    }

    public static function redirect(string $path): void
    {
        header('Location: ' . $path);
        exit;
    }
}
