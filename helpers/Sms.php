<?php

declare(strict_types=1);

final class Sms
{
    private static function config(string $key, string $default = ''): string
    {
        $value = getenv($key);
        return $value !== false ? $value : $default;
    }

    public static function enabled(): bool
    {
        return self::config('SMS_ENABLED') === 'true';
    }

    public static function send(string $to, string $message): array
    {
        $endpoint = self::config('SMS_ENDPOINT', 'https://comms.egosms.co/api/v1/json/');
        $username = self::config('SMS_USERNAME');
        $apiKey = self::config('SMS_API_KEY');
        $senderId = self::config('SMS_SENDER_ID');

        if (!$username || !$apiKey || !$senderId) {
            return ['success' => false, 'error' => 'SMS not configured (missing credentials)'];
        }

        $normalized = self::normalizePhone($to);
        if (!$normalized || strlen($normalized) < 10) {
            return ['success' => false, 'error' => 'Invalid phone number'];
        }

        $payload = [
            'method' => 'SendSms',
            'userdata' => ['username' => $username, 'password' => $apiKey],
            'msgdata' => [[
                'number' => $normalized,
                'message' => $message,
                'senderid' => $senderId,
                'priority' => '0',
            ]],
        ];

        try {
            $ch = curl_init($endpoint);
            curl_setopt_array($ch, [
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => json_encode($payload),
                CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_TIMEOUT => 15,
                CURLOPT_CONNECTTIMEOUT => 10,
            ]);
            $body = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $curlError = curl_error($ch);
            curl_close($ch);

            if ($curlError) {
                return ['success' => false, 'error' => "SMS gateway error: $curlError"];
            }

            $json = json_decode($body, true);
            if ($httpCode !== 200 || !$json || ($json['Status'] ?? '') !== 'OK') {
                $msg = $json['Message'] ?? $json['message'] ?? "HTTP $httpCode";
                return ['success' => false, 'error' => "SMS failed: $msg"];
            }

            return ['success' => true, 'error' => null];
        } catch (Throwable $e) {
            return ['success' => false, 'error' => 'SMS exception: ' . $e->getMessage()];
        }
    }

    public static function sendAcknowledgment(string $to, string $name, string $appName): array
    {
        $greeting = $name ? explode(' ', $name)[0] : 'there';
        $message = "Hi $greeting, thank you for your feedback! We have received it and will review it shortly. - $appName";
        return self::send($to, $message);
    }

    public static function normalizePhone(string $phone): string
    {
        $raw = trim($phone);
        $digits = preg_replace('/\D/', '', $raw);
        if (str_starts_with($raw, '+')) {
            $digits = $raw[0] . $digits;
        }
        return ltrim($digits, '+');
    }
}
