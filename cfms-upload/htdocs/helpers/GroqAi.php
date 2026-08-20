<?php

declare(strict_types=1);

final class GroqAi
{
    private const API_URL = 'https://api.groq.com/openai/v1/chat/completions';
    private const DEFAULT_MODEL = 'llama-3.1-8b-instant';
    private const FALLBACK_MODELS = ['llama-3.3-70b-versatile', 'llama-3.1-8b-instant', 'mixtral-8x7b-32768'];
    private const MAX_SMS_CHARS = 155;

    public static function enabled(): bool
    {
        return self::cfg('GROQ_API_KEY', '') !== '';
    }

    public static function generateSmsReply(array $feedback): string
    {
        $apiKey = trim(self::cfg('GROQ_API_KEY', ''));
        if ($apiKey === '') {
            throw new RuntimeException('Missing GROQ_API_KEY');
        }

        $brand = self::cfg('AI_BRAND_NAME', 'FeedbackHub');
        $models = self::uniqueModels();
        $lastErr = null;

        foreach ($models as $model) {
            try {
                return self::generateWithModel($feedback, $model, $apiKey, $brand);
            } catch (Throwable $e) {
                $lastErr = $e;
                error_log('[auto-ai-sms] Groq model failed: ' . $model . ' - ' . $e->getMessage());
            }
        }

        throw $lastErr ?: new RuntimeException('All Groq models failed');
    }

    private static function generateWithModel(array $feedback, string $model, string $apiKey, string $brand): string
    {
        $name = trim((string) ($feedback['name'] ?? ''));
        $type = trim((string) ($feedback['type'] ?? 'unknown'));
        $category = trim((string) ($feedback['category'] ?? 'General'));
        $rating = isset($feedback['rating']) && $feedback['rating'] !== '' ? (int) $feedback['rating'] : null;
        $message = trim((string) ($feedback['message'] ?? ''));

        $userBlock = "Customer name: " . ($name ?: 'Guest') . "\n"
            . "Feedback type: {$type}\n"
            . "Category: {$category}\n"
            . "Rating (1-5): " . ($rating !== null ? (string) $rating : 'n/a') . "\n"
            . "Message: {$message}";

        $systemPrompt = "You are the SMS assistant for {$brand}. Write ONE SMS reply to the customer based on their feedback.\n"
            . "Rules:\n"
            . "- Under " . self::MAX_SMS_CHARS . " characters total (strict).\n"
            . "- Professional, warm, no markdown, no quotes, no emojis unless one fits naturally.\n"
            . "- If the feedback is a complaint or very low rating: apologize briefly and say a team member will follow up (do not promise a specific time).\n"
            . "- If compliment: thank them sincerely.\n"
            . "- If suggestion: thank them and say we will consider it.\n"
            . "Output ONLY the SMS body text, nothing else.";

        $payload = json_encode([
            'model' => $model,
            'max_tokens' => 180,
            'temperature' => 0.5,
            'messages' => [
                ['role' => 'system', 'content' => $systemPrompt],
                ['role' => 'user', 'content' => $userBlock],
            ],
        ]);

        $ch = curl_init(self::API_URL);
        curl_setopt_array($ch, [
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $payload,
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $apiKey,
            ],
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_CONNECTTIMEOUT => 10,
        ]);
        $body = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);

        if ($curlError) {
            throw new RuntimeException('Groq API error: ' . $curlError);
        }

        $json = json_decode($body, true);
        if ($httpCode !== 200 || !$json) {
            $msg = $json['error']['message'] ?? $json['message'] ?? "HTTP {$httpCode}";
            throw new RuntimeException('Groq API error: ' . $msg);
        }

        $content = trim((string) ($json['choices'][0]['message']['content'] ?? ''));
        if ($content === '') {
            throw new RuntimeException('Empty AI response');
        }

        return self::truncateSms(preg_replace('/^["\']|["\']$/', '', $content));
    }

    public static function buildFallbackSms(array $feedback): string
    {
        $brand = self::cfg('AI_BRAND_NAME', 'FeedbackHub');
        $name = trim((string) ($feedback['name'] ?? ''));
        $greeting = $name ? explode(' ', $name)[0] : 'there';
        $base = "Hi {$greeting}, thanks for your feedback! We'll review it and follow up if needed. - {$brand}";
        return self::truncateSms($base);
    }

    public static function truncateSms(string $text): string
    {
        $t = preg_replace('/\s+/', ' ', trim($text));
        if (strlen($t) <= self::MAX_SMS_CHARS) {
            return $t;
        }
        return mb_substr($t, 0, self::MAX_SMS_CHARS - 1) . "\xe2\x80\xa9";
    }

    private static function uniqueModels(): array
    {
        $primary = trim(self::cfg('GROQ_MODEL', 'llama-3.1-8b-instant'));
        $chain = [];
        if ($primary !== '') {
            $chain[] = $primary;
        }
        foreach (self::FALLBACK_MODELS as $m) {
            if (!in_array($m, $chain, true)) {
                $chain[] = $m;
            }
        }
        if (!in_array(self::DEFAULT_MODEL, $chain, true)) {
            $chain[] = self::DEFAULT_MODEL;
        }
        return $chain;
    }

    private static function cfg(string $key, string $default = ''): string
    {
        if (function_exists('config_env')) {
            return config_env($key, $default);
        }
        $value = getenv($key);
        return $value !== false && $value !== '' ? $value : $default;
    }
}
