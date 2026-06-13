<?php

namespace App\Models;

use App\Core\App;

class Cart
{
    public static function findById(string $id): ?array
    {
        return App::db()->fetch('SELECT * FROM carts WHERE id = ?', [$id]);
    }

    public static function findByShopId(string $shopId): array
    {
        return App::db()->fetchAll(
            'SELECT * FROM carts WHERE shop_id = ? ORDER BY updated_at DESC',
            [$shopId]
        );
    }

    public static function create(string $shopId): string
    {
        return App::db()->insert('carts', ['shop_id' => $shopId]);
    }

    public static function touch(string $id): void
    {
        App::db()->query(
            'UPDATE carts SET updated_at = NOW() WHERE id = ?',
            [$id]
        );
    }

    public static function getItems(string $cartId): array
    {
        return App::db()->fetchAll(
            'SELECT ci.*, p.name, p.price as current_price, p.currency, p.image_url, p.type
             FROM cart_items ci
             JOIN products p ON p.id = ci.product_id
             WHERE ci.cart_id = ?
             ORDER BY ci.created_at',
            [$cartId]
        );
    }

    public static function getTotal(string $cartId): float
    {
        $result = App::db()->fetch(
            'SELECT COALESCE(SUM(ci.quantity * ci.price_at_add), 0) as total
             FROM cart_items ci
             WHERE ci.cart_id = ?',
            [$cartId]
        );
        return (float) ($result['total'] ?? 0);
    }

    public static function delete(string $id): void
    {
        App::db()->query('DELETE FROM carts WHERE id = ?', [$id]);
    }
}
