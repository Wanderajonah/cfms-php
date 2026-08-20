<?php

declare(strict_types=1);

final class Auth
{
    public static function user(): ?array
    {
        return $_SESSION['user'] ?? null;
    }

    public static function id(): ?int
    {
        return isset($_SESSION['user']['id']) ? (int) $_SESSION['user']['id'] : null;
    }

    public static function check(): bool
    {
        return self::user() !== null;
    }

    public static function require(): void
    {
        if (!self::check()) {
            Response::redirect('/login');
        }
    }

    public static function requireRole(string ...$roles): void
    {
        self::require();
        $role = $_SESSION['user']['role_slug'] ?? '';
        if (!in_array($role, $roles, true)) {
            http_response_code(403);
            View::render('errors/403', ['title' => 'Forbidden'], self::check() ? 'app' : 'public');
            exit;
        }
    }

    public static function requireApiRole(string ...$roles): array
    {
        $user = self::apiUser();
        if (!$user) {
            Response::json(['message' => 'Missing Authorization token'], 401);
        }
        if (!in_array($user['role_slug'], $roles, true)) {
            Response::json(['message' => 'Forbidden'], 403);
        }
        return $user;
    }

    public static function apiUser(): ?array
    {
        $header = $_SERVER['HTTP_AUTHORIZATION'] ?? '';
        if (!preg_match('/^Bearer\s+(.+)$/', $header, $matches)) {
            return self::user();
        }
        $token = hash('sha256', $matches[1]);
        return (new User())->findByRememberTokenHash($token);
    }

    public static function login(array $user, bool $remember = false): ?string
    {
        session_regenerate_id(true);
        $_SESSION['user'] = self::sessionUser($user);
        $_SESSION['last_activity'] = time();
        (new AuditLog())->record((int) $user['id'], 'login', 'users', (int) $user['id'], 'User logged in');

        if (!$remember) {
            return null;
        }
        $plain = bin2hex(random_bytes(32));
        (new User())->storeRememberToken((int) $user['id'], hash('sha256', $plain));
        setcookie('remember_token', $plain, [
            'expires' => time() + 60 * 60 * 24 * 7,
            'path' => '/',
            'httponly' => true,
            'samesite' => 'Lax',
            'secure' => (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off'),
        ]);
        return $plain;
    }

    public static function logout(): void
    {
        if (self::id()) {
            (new AuditLog())->record(self::id(), 'logout', 'users', self::id(), 'User logged out');
            (new User())->storeRememberToken(self::id(), null);
        }
        setcookie('remember_token', '', time() - 3600, '/');
        session_unset();
        session_destroy();
    }

    public static function attemptRememberLogin(): void
    {
        if (self::check() || empty($_COOKIE['remember_token'])) {
            return;
        }
        $user = (new User())->findByRememberTokenHash(hash('sha256', (string) $_COOKIE['remember_token']));
        if ($user && (int) $user['is_active'] === 1) {
            $_SESSION['user'] = self::sessionUser($user);
            $_SESSION['last_activity'] = time();
        }
    }

    private static function sessionUser(array $user): array
    {
        return [
            'id' => (int) $user['id'],
            'email' => $user['email'],
            'name' => $user['name'],
            'role_slug' => $user['role_slug'],
            'role_name' => $user['role_name'],
            'avatar_url' => $user['avatar_url'] ?? null,
        ];
    }
}
