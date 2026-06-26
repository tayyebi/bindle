<?php

namespace App\Models;

use App\Core\App;

class Shop
{
    public static function findByDomain(string $domain): ?array
    {
        return App::db()->fetch(
            'SELECT * FROM shops WHERE domain = ?',
            [$domain]
        );
    }

    public static function findById(string $id): ?array
    {
        return App::db()->fetch(
            'SELECT * FROM shops WHERE id = ?',
            [$id]
        );
    }

    public static function findByEmail(string $email): ?array
    {
        return App::db()->fetch(
            'SELECT * FROM shops WHERE email = ?',
            [$email]
        );
    }

    public static function all(): array
    {
        return App::db()->fetchAll('SELECT * FROM shops ORDER BY created_at DESC');
    }

    public static function create(array $data): string
    {
        return App::db()->insert('shops', [
            'domain' => $data['domain'],
            'name' => $data['name'],
            'email' => $data['email'],
            'password_hash' => password_hash($data['password'], PASSWORD_BCRYPT),
            'payment_instructions' => $data['payment_instructions'] ?? '',
            'webhook_url' => $data['webhook_url'] ?? '',
            'is_active' => $data['is_active'] ?? true,
        ]);
    }

    public static function update(string $id, array $data): void
    {
        $allowed = ['name', 'email', 'payment_instructions', 'webhook_url', 'is_active', 'checkout_fields'];
        $updateData = [];
        foreach ($allowed as $key) {
            if (isset($data[$key])) {
                $updateData[$key] = $data[$key];
            }
        }
        if (!empty($updateData)) {
            App::db()->update('shops', $updateData, 'id = :id', ['id' => $id]);
        }
    }

    public static function toggleActive(string $id): void
    {
        App::db()->query(
            'UPDATE shops SET is_active = NOT is_active, updated_at = NOW() WHERE id = ?',
            [$id]
        );
    }

    public static function verifyPassword(string $email, string $password): ?array
    {
        $shop = self::findByEmail($email);
        if ($shop && password_verify($password, $shop['password_hash'])) {
            return $shop;
        }
        return null;
    }

    public static function updatePassword(string $id, string $hash): void
    {
        App::db()->query(
            'UPDATE shops SET password_hash = ?, updated_at = NOW() WHERE id = ?',
            [$hash, $id]
        );
    }

    public static function enableTotp(string $id, string $secret): void
    {
        App::db()->query(
            'UPDATE shops SET totp_secret = ?, totp_enabled = true, updated_at = NOW() WHERE id = ?',
            [$secret, $id]
        );
    }

    public static function disableTotp(string $id): void
    {
        App::db()->query(
            'UPDATE shops SET totp_secret = \'\', totp_enabled = false, updated_at = NOW() WHERE id = ?',
            [$id]
        );
    }

    public static function delete(string $id): void
    {
        App::db()->query('DELETE FROM shops WHERE id = ?', [$id]);
    }
}
