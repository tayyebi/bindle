<?php

namespace App\Models;

use App\Core\App;

class Product
{
    public static function findById(string $id): ?array
    {
        return App::db()->fetch('SELECT * FROM products WHERE id = ?', [$id]);
    }

    public static function findByUrl(string $shopId, string $url): ?array
    {
        return App::db()->fetch(
            'SELECT * FROM products WHERE shop_id = ? AND url = ?',
            [$shopId, $url]
        );
    }

    public static function findByShopId(string $shopId): array
    {
        return App::db()->fetchAll(
            'SELECT * FROM products WHERE shop_id = ? ORDER BY created_at DESC',
            [$shopId]
        );
    }

    public static function createOrUpdate(string $shopId, array $data): string
    {
        $existing = self::findByUrl($shopId, $data['url']);
        if ($existing) {
            App::db()->update('products', [
                'name' => $data['name'],
                'price' => $data['price'],
                'currency' => $data['currency'] ?? 'USD',
                'type' => $data['type'] ?? 'physical',
                'description' => $data['description'] ?? '',
                'image_url' => $data['image_url'] ?? '',
                'stock' => $data['stock'] ?? null,
            ], 'id = :id', ['id' => $existing['id']]);
            return $existing['id'];
        }

        return App::db()->insert('products', [
            'shop_id' => $shopId,
            'url' => $data['url'],
            'name' => $data['name'],
            'price' => $data['price'],
            'currency' => $data['currency'] ?? 'USD',
            'type' => $data['type'] ?? 'physical',
            'description' => $data['description'] ?? '',
            'image_url' => $data['image_url'] ?? '',
            'stock' => $data['stock'] ?? null,
        ]);
    }
}
