<?php

namespace App\Core;

class App
{
    private array $config;
    private Router $router;
    private Database $db;
    private Request $request;

    public function __construct(array $config)
    {
        $this->config = $config;
        $this->db = new Database($config['db']);
        $this->request = new Request();
        $this->router = new Router($this->request, $config, $this->db);
    }

    public function run(): void
    {
        try {
            $this->registerRoutes();
            $this->router->dispatch();
        } catch (\Throwable $e) {
            \App\Services\Logger::log('critical', $e->getMessage(), $e->getFile(), $e->getLine(), $e->getTraceAsString());
            http_response_code(500);
            echo View::render('errors/500', [
                'message' => $this->config['app']['env'] === 'development'
                    ? $e->getMessage() : 'خطای داخلی سرور'
            ], true, 'admin');
        }
    }

    private function registerRoutes(): void
    {
        $this->router->registerShopRoutes();
        $this->router->registerAdminRoutes();
    }

    public function getConfig(): array
    {
        return $this->config;
    }

    public static function config(string $key, mixed $default = null): mixed
    {
        global $app;
        $keys = explode('.', $key);
        $value = $app->getConfig();
        foreach ($keys as $k) {
            if (!isset($value[$k])) return $default;
            $value = $value[$k];
        }
        return $value;
    }

    public static function db(): Database
    {
        global $app;
        return $app->db;
    }
}
