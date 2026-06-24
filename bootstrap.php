<?php

session_start();

spl_autoload_register(function ($class) {
    $prefixes = [
        'App\\Core\\' => __DIR__ . '/src/Core/',
        'App\\Controllers\\' => __DIR__ . '/src/Controllers/',
        'App\\Models\\' => __DIR__ . '/src/Models/',
        'App\\Services\\' => __DIR__ . '/src/Services/',
        'App\\Middleware\\' => __DIR__ . '/src/Middleware/',
    ];

    foreach ($prefixes as $prefix => $baseDir) {
        $len = strlen($prefix);
        if (strncmp($prefix, $class, $len) !== 0) continue;
        $relativeClass = substr($class, $len);
        $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';
        if (file_exists($file)) {
            require $file;
            return;
        }
    }
});

require_once __DIR__ . '/src/helpers.php';

$config = require __DIR__ . '/config/app.php';
$app = new App\Core\App($config);
$GLOBALS['app'] = $app;

App\Services\Logger::register();

$app->run();
