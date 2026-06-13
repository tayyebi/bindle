<?php

require_once __DIR__ . '/../bootstrap.php';

$db = App\Core\App::db();

echo "Seeding system admin...\n";

$existing = \App\Models\SystemAdmin::findByUsername('admin');
if ($existing) {
    echo "Admin already exists.\n";
} else {
    \App\Models\SystemAdmin::create([
        'username' => 'admin',
        'password' => 'admin123',
    ]);
    echo "Admin created: admin / admin123\n";
}
