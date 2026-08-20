<?php

declare(strict_types=1);

final class Security
{
    public static function e(?string $value): string
    {
        return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
    }

    public static function csrfToken(): string
    {
        if (empty($_SESSION['_csrf'])) {
            $_SESSION['_csrf'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['_csrf'];
    }

    public static function verifyCsrf(): void
    {
        $token = $_POST['_csrf'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
        if (!hash_equals($_SESSION['_csrf'] ?? '', (string) $token)) {
            if (str_starts_with($_SERVER['REQUEST_URI'] ?? '', '/api/')) {
                Response::json(['message' => 'Invalid CSRF token'], 419);
            }
            Flash::error('Your session token expired. Please try again.');
            Response::redirect($_SERVER['HTTP_REFERER'] ?? '/');
        }
    }

    public static function enforceSessionTimeout(int $seconds): void
    {
        if (empty($_SESSION['user'])) {
            return;
        }
        $last = (int) ($_SESSION['last_activity'] ?? time());
        if ((time() - $last) > $seconds) {
            session_unset();
            session_destroy();
            session_start();
            Flash::error('Your session timed out. Please log in again.');
            return;
        }
        $_SESSION['last_activity'] = time();
    }

    public static function input(string $key, mixed $default = null): mixed
    {
        return $_POST[$key] ?? $_GET[$key] ?? $default;
    }
}
