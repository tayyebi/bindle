<?php

namespace App\Models;

use App\Core\App;

class CartItem
{
    public static function findByCartAndProduct(string $cartId, string $productId): ?array
    {
        return App::db()->fetch(
            'SELECT * FROM cart_items WHERE cart_id = ? AND product_id = ?',
            [$cartId, $productId]
        );
    }

    public static function add(string $cartId, string $productId, int $quantity, float $price): string
    {
        $existing = self::findByCartAndProduct($cartId, $productId);
        if ($existing) {
            App::db()->query(
                'UPDATE cart_items SET quantity = quantity + ? WHERE id = ?',
                [$quantity, $existing['id']]
            );
            return $existing['id'];
        }

        return App::db()->insert('cart_items', [
            'cart_id' => $cartId,
            'product_id' => $productId,
            'quantity' => $quantity,
            'price_at_add' => $price,
        ]);
    }

    public static function updateQuantity(string $itemId, int $quantity): void
    {
        App::db()->query(
            'UPDATE cart_items SET quantity = ? WHERE id = ?',
            [$quantity, $itemId]
        );
    }

    public static function remove(string $itemId): void
    {
        App::db()->query('DELETE FROM cart_items WHERE id = ?', [$itemId]);
    }

    public static function removeByCart(string $cartId): void
    {
        App::db()->query('DELETE FROM cart_items WHERE cart_id = ?', [$cartId]);
    }
}
