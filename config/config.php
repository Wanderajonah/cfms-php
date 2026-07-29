<?php

declare(strict_types=1);

return [
    'app' => [
        'name' => getenv('APP_NAME') ?: 'Cafe Javas Feedback Management',
        'base_url' => getenv('APP_URL') ?: '',
        'timezone' => getenv('APP_TIMEZONE') ?: 'Africa/Kampala',
        'session_timeout' => 60 * 60 * 2,
        'remember_days' => 7,
        'upload_path' => __DIR__ . '/../assets/uploads',
        'upload_url' => '/assets/uploads',
    ],
    'db' => [
        'host' => getenv('DB_HOST') ?: '127.0.0.1',
        'port' => getenv('DB_PORT') ?: '3306',
        'database' => getenv('DB_DATABASE') ?: 'customer_feedback_system',
        'username' => getenv('DB_USERNAME') ?: 'root',
        'password' => getenv('DB_PASSWORD') ?: '',
        'charset' => 'utf8mb4',
    ],
    'sms' => [
        'enabled' => getenv('SMS_ENABLED') ?: 'false',
        'endpoint' => getenv('SMS_ENDPOINT') ?: 'https://comms.egosms.co/api/v1/json/',
        'username' => getenv('SMS_USERNAME') ?: '',
        'api_key' => getenv('SMS_API_KEY') ?: '',
        'sender_id' => getenv('SMS_SENDER_ID') ?: '',
    ],
    'ai' => [
        'groq_api_key' => getenv('GROQ_API_KEY') ?: '',
        'groq_model' => getenv('GROQ_MODEL') ?: 'llama-3.1-8b-instant',
        'brand_name' => getenv('AI_BRAND_NAME') ?: 'Cafe Javas',
    ],
];
