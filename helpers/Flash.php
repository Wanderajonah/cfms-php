<?php

declare(strict_types=1);

final class Flash
{
    public static function success(string $message): void
    {
        $_SESSION['flash'][] = ['type' => 'success', 'message' => $message];
    }

    public static function error(string $message): void
    {
        $_SESSION['flash'][] = ['type' => 'danger', 'message' => $message];
    }

    public static function all(): array
    {
        $messages = $_SESSION['flash'] ?? [];
        unset($_SESSION['flash']);
        return $messages;
    }
}
