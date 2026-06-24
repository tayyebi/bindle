<?php

namespace App\Models;

use App\Core\App;

class SystemAdmin
{
    public static function findByUsername(string $username): ?array
    {
        return App::db()->fetch(
            'SELECT * FROM system_admins WHERE username = ?',
            [$username]
        );
    }

    public static function create(array $data): string
    {
        return App::db()->insert('system_admins', [
            'username' => $data['username'],
            'password_hash' => password_hash($data['password'], PASSWORD_BCRYPT),
        ]);
    }

    public static function findById(string $id): ?array
    {
        return App::db()->fetch(
            'SELECT * FROM system_admins WHERE id = ?',
            [$id]
        );
    }

    public static function verifyPassword(string $username, string $password): ?array
    {
        $admin = self::findByUsername($username);
        if ($admin && password_verify($password, $admin['password_hash'])) {
            return $admin;
        }
        return null;
    }

    public static function updatePassword(string $id, string $hash): void
    {
        App::db()->query(
            'UPDATE system_admins SET password_hash = ?, created_at = NOW() WHERE id = ?',
            [$hash, $id]
        );
    }

    public static function enableTotp(string $id, string $secret): void
    {
        App::db()->query(
            'UPDATE system_admins SET totp_secret = ?, totp_enabled = true, created_at = NOW() WHERE id = ?',
            [$secret, $id]
        );
    }

    public static function disableTotp(string $id): void
    {
        App::db()->query(
            'UPDATE system_admins SET totp_secret = \'\', totp_enabled = false, created_at = NOW() WHERE id = ?',
            [$id]
        );
    }
}
