<?php

namespace App\Models;

use App\Core\App;

class DownloadToken
{
    public static function findByToken(string $token): ?array
    {
        return App::db()->fetch(
            'SELECT * FROM download_tokens WHERE token = ? AND used_at IS NULL AND expires_at > NOW()',
            [$token]
        );
    }

    public static function create(string $orderId, string $productId, int $expiresInDays = 7): string
    {
        $token = bin2hex(random_bytes(64));
        return App::db()->insert('download_tokens', [
            'order_id' => $orderId,
            'product_id' => $productId,
            'token' => $token,
            'expires_at' => date('Y-m-d H:i:s', time() + $expiresInDays * 86400),
        ]);
    }

    public static function markUsed(string $id): void
    {
        App::db()->query(
            'UPDATE download_tokens SET used_at = NOW() WHERE id = ?',
            [$id]
        );
    }
}
