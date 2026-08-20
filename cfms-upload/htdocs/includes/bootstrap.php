<?php

declare(strict_types=1);

if (is_file(__DIR__ . '/../.env')) {
    foreach (file(__DIR__ . '/../.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
        $line = trim($line);
        if ($line === '' || str_starts_with($line, '#') || !str_contains($line, '=')) {
            continue;
        }
        [$key, $value] = array_map('trim', explode('=', $line, 2));
        $value = trim($value, "\"'");
        if (getenv($key) === false) {
            if (function_exists('putenv')) {
                putenv($key . '=' . $value);
            }
            $_ENV[$key] = $value;
        }
    }
}

$config = require __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';
date_default_timezone_set($config['app']['timezone']);

spl_autoload_register(static function (string $class): void {
    $roots = ['controllers', 'models', 'middleware', 'helpers', 'includes', 'config'];
    foreach ($roots as $root) {
        $path = __DIR__ . '/../' . $root . '/' . $class . '.php';
        if (is_file($path)) {
            require_once $path;
            return;
        }
    }
});

set_exception_handler(static function (Throwable $e): void {
    $isApi = str_starts_with($_SERVER['REQUEST_URI'] ?? '', '/api/');
    if ($isApi) {
        http_response_code(500);
        Response::json(['message' => $e instanceof PDOException ? 'Database connection failed' : 'Server error'], 500);
    }
    http_response_code($e instanceof PDOException ? 200 : 500);
    $title = 'Application Setup Required';
    $message = $e instanceof PDOException
        ? 'The application could not connect to MySQL. Check DB_HOST, DB_DATABASE, DB_USERNAME, and DB_PASSWORD in customer-feedback-system/.env, then import database/migration.sql and database/seed.sql.'
        : $e->getMessage();
    View::render('errors/setup', compact('title', 'message'), Auth::check() ? 'app' : 'public');
});

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_name('cjfms_session');
    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'httponly' => true,
        'samesite' => 'Lax',
        'secure' => (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off'),
    ]);
    session_start();
}

Security::enforceSessionTimeout((int) $config['app']['session_timeout']);
Auth::attemptRememberLogin();
