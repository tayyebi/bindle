<?php

namespace App\Services;

use App\Core\App;

class EventService
{
    public const EVENTS = [
        'shop.created' => 'ایجاد فروشگاه جدید',
        'shop.toggled' => 'فعال/غیرفعال شدن فروشگاه',
        'order.created' => 'ثبت سفارش جدید',
        'order.approved' => 'تأیید سفارش',
        'order.rejected' => 'رد سفارش',
        'error.critical' => 'خطای بحرانی',
        'error.error' => 'خطا',
        'error.warning' => 'هشدار',
        'test' => 'رویداد تست',
    ];

    public static function getAvailableEvents(): array
    {
        return self::EVENTS;
    }

    public static function dispatch(string $event, array $payload, ?string $shopId = null): void
    {
        $body = json_encode([
            'event' => $event,
            'data' => $payload,
            'timestamp' => time(),
        ], JSON_UNESCAPED_UNICODE);

        self::dispatchToSystemWebhooks($event, $body, $payload);

        if ($shopId) {
            self::dispatchToShopWebhook($shopId, $event, $body);
        }
    }

    private static function dispatchToSystemWebhooks(string $event, string $body, array $payload): void
    {
        $webhooks = App::db()->fetchAll(
            'SELECT * FROM system_webhooks WHERE is_active = true'
        );

        foreach ($webhooks as $webhook) {
            $registeredEvents = explode(',', $webhook['events']);
            $registeredEvents = array_map('trim', $registeredEvents);

            if (!in_array($event, $registeredEvents, true)) {
                continue;
            }

            try {
                $ch = curl_init($webhook['url']);
                curl_setopt_array($ch, [
                    CURLOPT_POST => true,
                    CURLOPT_POSTFIELDS => $body,
                    CURLOPT_HTTPHEADER => [
                        'Content-Type: application/json',
                        'User-Agent: Bindle-System/1.0',
                        'X-Bindle-Event: ' . $event,
                    ],
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_TIMEOUT => 10,
                    CURLOPT_CONNECTTIMEOUT => 5,
                ]);
                curl_exec($ch);
                curl_close($ch);
            } catch (\Throwable $e) {
                error_log("System webhook dispatch error: " . $e->getMessage());
            }
        }
    }

    private static function dispatchToShopWebhook(string $shopId, string $event, string $body): void
    {
        $shop = \App\Models\Shop::findById($shopId);
        if (!$shop || empty($shop['webhook_url'])) return;

        $url = $shop['webhook_url'];

        try {
            $ch = curl_init($url);
            curl_setopt_array($ch, [
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

            \App\Models\WebhookLog::create([
                'shop_id' => $shopId,
                'event' => $event,
                'url' => $url,
                'request_body' => $body,
                'response_status' => $httpCode ?: null,
            ]);
        } catch (\Throwable $e) {
            error_log("Shop webhook dispatch error: " . $e->getMessage());
        }
    }
}
