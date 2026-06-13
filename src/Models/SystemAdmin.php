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

    public static function verifyPassword(string $username, string $password): ?array
    {
        $admin = self::findByUsername($username);
        if ($admin && password_verify($password, $admin['password_hash'])) {
            return $admin;
        }
        return null;
    }
}
