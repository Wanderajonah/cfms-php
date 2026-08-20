<?php

declare(strict_types=1);

if (!function_exists('config_env')) {
    function config_env(string $key, string $default = ''): string
    {
        $value = $_ENV[$key] ?? getenv($key);
        if ($value === false || $value === '') {
            return $default;
        }
        return (string) $value;
    }
}

return [
    'app' => [
        'name' => config_env('APP_NAME', 'Cafe Javas Feedback Management'),
        'base_url' => config_env('APP_URL', ''),
        'timezone' => config_env('APP_TIMEZONE', 'Africa/Kampala'),
        'session_timeout' => 60 * 60 * 2,
        'remember_days' => 7,
        'upload_path' => __DIR__ . '/../assets/uploads',
        'upload_url' => '/assets/uploads',
    ],
    'db' => [
        'host' => config_env('DB_HOST', '127.0.0.1'),
        'port' => config_env('DB_PORT', '3306'),
        'database' => config_env('DB_DATABASE', 'customer_feedback_system'),
        'username' => config_env('DB_USERNAME', 'root'),
        'password' => config_env('DB_PASSWORD', ''),
        'charset' => 'utf8mb4',
    ],
    'sms' => [
        'enabled' => config_env('SMS_ENABLED', 'true'),
        'endpoint' => config_env('SMS_ENDPOINT', 'https://comms.egosms.co/api/v1/json/'),
        'username' => config_env('SMS_USERNAME', 'bagumajessysmith'),
        'api_key' => config_env('SMS_API_KEY', ''),
        'sender_id' => config_env('SMS_SENDER_ID', 'FeedbackHub'),
    ],
    'ai' => [
        'groq_api_key' => config_env('GROQ_API_KEY', ''),
        'groq_model' => config_env('GROQ_MODEL', 'llama-3.1-8b-instant'),
        'brand_name' => config_env('AI_BRAND_NAME', 'FeedbackHub'),
    ],
];
