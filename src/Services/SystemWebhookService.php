<?php

namespace App\Services;

use App\Core\App;

class SystemWebhookService
{
    public static function dispatch(string $event, array $payload): void
    {
        $webhooks = App::db()->fetchAll(
            'SELECT * FROM system_webhooks WHERE is_active = true AND events LIKE :event',
            ['event' => '%' . $event . '%']
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
                    CURLOPT_POSTFIELDS => json_encode($payload, JSON_UNESCAPED_UNICODE),
                    CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
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

    public static function getAll(): array
    {
        return App::db()->fetchAll(
            'SELECT * FROM system_webhooks ORDER BY created_at DESC'
        );
    }

    public static function findById(string $id): ?array
    {
        return App::db()->fetch(
            'SELECT * FROM system_webhooks WHERE id = ?',
            [$id]
        );
    }

    public static function create(array $data): string
    {
        return App::db()->insert('system_webhooks', [
            'url' => $data['url'],
            'events' => $data['events'] ?? '',
            'is_active' => $data['is_active'] ?? true,
            'description' => $data['description'] ?? '',
        ]);
    }

    public static function update(string $id, array $data): void
    {
        $allowed = ['url', 'events', 'is_active', 'description'];
        $updateData = [];
        foreach ($allowed as $key) {
            if (isset($data[$key])) {
                $updateData[$key] = $data[$key];
            }
        }
        $updateData['updated_at'] = date('c');
        if (!empty($updateData)) {
            App::db()->update('system_webhooks', $updateData, 'id = :id', ['id' => $id]);
        }
    }

    public static function delete(string $id): void
    {
        App::db()->query('DELETE FROM system_webhooks WHERE id = ?', [$id]);
    }

    public static function getRegisteredEvents(): array
    {
        $rows = App::db()->fetchAll('SELECT events FROM system_webhooks WHERE is_active = true');
        $allEvents = [];
        foreach ($rows as $row) {
            $parts = explode(',', $row['events']);
            foreach ($parts as $p) {
                $p = trim($p);
                if (!empty($p)) $allEvents[$p] = true;
            }
        }
        return array_keys($allEvents);
    }
}
