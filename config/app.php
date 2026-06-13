<?php

return [
    'db' => [
        'host' => $_ENV['DB_HOST'] ?? 'db',
        'port' => $_ENV['DB_PORT'] ?? '5432',
        'name' => $_ENV['DB_NAME'] ?? 'bindle',
        'user' => $_ENV['DB_USER'] ?? 'bindle',
        'password' => $_ENV['DB_PASSWORD'] ?? 'bindle',
    ],
    'app' => [
        'env' => $_ENV['APP_ENV'] ?? 'development',
        'key' => $_ENV['APP_KEY'] ?? '',
        'domain' => $_ENV['APP_DOMAIN'] ?? 'bindle.app',
        'name' => 'بقچه',
        'url' => '',
    ],
    'storage' => [
        'proofs' => __DIR__ . '/../storage/proofs',
    ],
];
