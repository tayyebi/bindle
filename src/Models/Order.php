<?php

namespace App\Models;

use App\Core\App;

class Order
{
    public static function findById(string $id): ?array
    {
        return App::db()->fetch('SELECT * FROM orders WHERE id = ?', [$id]);
    }

    public static function findByToken(string $token): ?array
    {
        return App::db()->fetch('SELECT * FROM orders WHERE token = ?', [$token]);
    }

    public static function findByShopId(string $shopId, string $status = ''): array
    {
        if ($status) {
            return App::db()->fetchAll(
                'SELECT o.*, COUNT(pp.id) as has_proof
                 FROM orders o
                 LEFT JOIN payment_proofs pp ON pp.order_id = o.id
                 WHERE o.shop_id = ? AND o.status = ?
                 GROUP BY o.id
                 ORDER BY o.created_at DESC',
                [$shopId, $status]
            );
        }
        return App::db()->fetchAll(
            'SELECT o.*, COUNT(pp.id) as has_proof
             FROM orders o
             LEFT JOIN payment_proofs pp ON pp.order_id = o.id
             WHERE o.shop_id = ?
             GROUP BY o.id
             ORDER BY o.created_at DESC',
            [$shopId]
        );
    }

    public static function create(array $data): string
    {
        return App::db()->insert('orders', [
            'shop_id' => $data['shop_id'],
            'cart_id' => $data['cart_id'] ?? null,
            'token' => bin2hex(random_bytes(32)),
            'status' => 'pending',
            'total' => $data['total'],
            'currency' => $data['currency'],
            'customer_name' => $data['customer_name'],
            'customer_email' => $data['customer_email'],
            'customer_phone' => $data['customer_phone'] ?? '',
            'shipping_address' => $data['shipping_address'] ?? '',
            'notes' => $data['notes'] ?? '',
        ]);
    }

    public static function approve(string $id): void
    {
        App::db()->query(
            'UPDATE orders SET status = ?, approved_at = NOW(), updated_at = NOW() WHERE id = ?',
            ['approved', $id]
        );
    }

    public static function reject(string $id): void
    {
        App::db()->query(
            'UPDATE orders SET status = ?, updated_at = NOW() WHERE id = ?',
            ['rejected', $id]
        );
    }

    public static function cancel(string $id): void
    {
        App::db()->query(
            'UPDATE orders SET status = ?, updated_at = NOW() WHERE id = ?',
            ['cancelled', $id]
        );
    }

    public static function getOrderProducts(string $orderId): array
    {
        return App::db()->fetchAll(
            'SELECT p.*, ci.quantity, ci.price_at_add
             FROM orders o
             JOIN carts c ON c.id = o.cart_id
             JOIN cart_items ci ON ci.cart_id = c.id
             JOIN products p ON p.id = ci.product_id
             WHERE o.id = ?',
            [$orderId]
        );
    }

    public static function getPaymentProof(string $orderId): ?array
    {
        return App::db()->fetch(
            'SELECT * FROM payment_proofs WHERE order_id = ? ORDER BY submitted_at DESC LIMIT 1',
            [$orderId]
        );
    }
}
