<?php

namespace App\Services;

use App\Models\WebhookLog;

class WebhookService
{
    public static function dispatch(string $shopId, string $event, array $payload): void
    {
        $shop = \App\Models\Shop::findById($shopId);
        if (!$shop || empty($shop['webhook_url'])) return;

        $url = $shop['webhook_url'];
        $body = json_encode([
            'event' => $event,
            'data' => $payload,
            'timestamp' => time(),
        ], JSON_UNESCAPED_UNICODE);

        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $body,
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'User-Agent: Bindle/1.0',
                'X-Bindle-Event: ' . $event,
            ],
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 10,
            CURLOPT_CONNECTTIMEOUT => 5,
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        WebhookLog::create([
            'shop_id' => $shopId,
            'event' => $event,
            'url' => $url,
            'request_body' => $body,
            'response_status' => $httpCode ?: null,
        ]);
    }
}
