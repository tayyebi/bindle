<?php

namespace App\Models;

use App\Core\App;

class WebhookLog
{
    public static function create(array $data): string
    {
        return App::db()->insert('webhook_logs', [
            'shop_id' => $data['shop_id'],
            'event' => $data['event'],
            'url' => $data['url'],
            'request_body' => $data['request_body'] ?? '',
            'response_status' => $data['response_status'] ?? null,
        ]);
    }
}
