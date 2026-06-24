<?php

namespace App\Core;

use App\Controllers\AdminController;
use App\Controllers\CartController;
use App\Controllers\CheckoutController;
use App\Controllers\DatabaseManagerController;
use App\Controllers\HomeController;
use App\Controllers\OrderController;
use App\Controllers\ProfileController;
use App\Controllers\ShopDashboardController;
use App\Controllers\SystemLogController;
use App\Controllers\SystemWebhookController;
use App\Models\Shop;

class Router
{
    private array $shopRoutes = [];
    private array $adminRoutes = [];
    private Request $request;
    private array $config;
    private Database $db;
    private ?array $shopContext = null;

    public function __construct(Request $request, array $config, Database $db)
    {
        $this->request = $request;
        $this->config = $config;
        $this->db = $db;
    }

    public function registerShopRoutes(): void
    {
        $this->shopRoutes = [
            'GET' => [
                '/' => [HomeController::class, 'index'],
                '/cart' => [CartController::class, 'index'],
                '/checkout' => [CheckoutController::class, 'index'],
                '/order/{token}' => [OrderController::class, 'show'],
                '/order/{token}/success' => [OrderController::class, 'success'],
                '/download/{token}' => [OrderController::class, 'download'],
            ],
            'POST' => [
                '/add' => [CartController::class, 'add'],
                '/cart/update' => [CartController::class, 'update'],
                '/cart/remove' => [CartController::class, 'remove'],
                '/checkout' => [CheckoutController::class, 'submit'],
                '/order/{id}/proof' => [OrderController::class, 'submitProof'],
            ],
        ];
    }

    public function registerAdminRoutes(): void
    {
        $this->adminRoutes = [
            'GET' => [
                '/' => [HomeController::class, 'landing'],
                '/login' => [AdminController::class, 'loginForm'],
                '/logout' => [AdminController::class, 'logout'],
                '/dashboard' => [ShopDashboardController::class, 'index'],
                '/dashboard/orders' => [ShopDashboardController::class, 'orders'],
                '/dashboard/orders/{id}' => [ShopDashboardController::class, 'orderDetail'],
                '/dashboard/settings' => [ShopDashboardController::class, 'settings'],
                '/admin' => [AdminController::class, 'index'],
                '/admin/login' => [AdminController::class, 'loginForm'],
                '/admin/shops' => [AdminController::class, 'shops'],
                '/admin/shops/create' => [AdminController::class, 'createShopForm'],
                '/profile' => [ProfileController::class, 'index'],
                '/profile/totp' => [ProfileController::class, 'totpSetup'],
                '/totp/verify' => [ProfileController::class, 'totpVerifyForm'],
                '/system/logs' => [SystemLogController::class, 'index'],
                '/system/logs/{id}' => [SystemLogController::class, 'detail'],
                '/system/logs/clear' => [SystemLogController::class, 'clear'],
                '/system/webhooks' => [SystemWebhookController::class, 'index'],
                '/system/webhooks/create' => [SystemWebhookController::class, 'createForm'],
                '/system/webhooks/edit/{id}' => [SystemWebhookController::class, 'editForm'],
                '/system/webhooks/toggle/{id}' => [SystemWebhookController::class, 'toggle'],
                '/system/webhooks/delete/{id}' => [SystemWebhookController::class, 'delete'],
                '/system/webhooks/test/{id}' => [SystemWebhookController::class, 'test'],
                '/system/database' => [DatabaseManagerController::class, 'index'],
                '/system/database/query' => [DatabaseManagerController::class, 'query'],
            ],
            'POST' => [
                '/login' => [AdminController::class, 'login'],
                '/admin/login' => [AdminController::class, 'login'],
                '/admin/shops/create' => [AdminController::class, 'createShop'],
                '/admin/shops/{id}/toggle' => [AdminController::class, 'toggleShop'],
                '/dashboard/orders/{id}/approve' => [ShopDashboardController::class, 'approveOrder'],
                '/dashboard/orders/{id}/reject' => [ShopDashboardController::class, 'rejectOrder'],
                '/dashboard/settings' => [ShopDashboardController::class, 'updateSettings'],
                '/profile/password' => [ProfileController::class, 'updatePassword'],
                '/profile/totp/enable' => [ProfileController::class, 'totpEnable'],
                '/profile/totp/disable' => [ProfileController::class, 'totpDisable'],
                '/totp/verify' => [ProfileController::class, 'totpVerify'],
                '/system/webhooks/create' => [SystemWebhookController::class, 'create'],
                '/system/webhooks/edit/{id}' => [SystemWebhookController::class, 'edit'],
                '/system/database/query' => [DatabaseManagerController::class, 'query'],
            ],
        ];
    }

    public function dispatch(): void
    {
        $method = $this->request->method();
        if ($method === 'HEAD') {
            $method = 'GET';
        }
        $uri = $this->request->uri();
        $host = $this->request->host();

        $isShopDomain = $this->resolveShopContext($host);

        if ($isShopDomain && $this->shopContext) {
            $this->request->setShopContext($this->shopContext);
            $handler = $this->matchRoute($method, $uri, $this->shopRoutes);
            if ($handler) {
                $this->executeHandler($handler);
                return;
            }
            $this->notFound();
            return;
        }

        $handler = $this->matchRoute($method, $uri, $this->adminRoutes);
        if ($handler) {
            $this->executeHandler($handler);
            return;
        }

        if (!$isShopDomain) {
            $handler = $this->matchRoute($method, $uri, $this->shopRoutes);
            if ($handler) {
                $this->executeHandler($handler);
                return;
            }
        }

        $this->notFound();
    }

    private function resolveShopContext(string $host): bool
    {
        $mainDomain = $this->config['app']['domain'];
        $port = '';
        if (str_contains($host, ':')) {
            [$host, $port] = explode(':', $host, 2);
        }

        if ($host === $mainDomain || str_ends_with($host, '.' . $mainDomain)) {
            return false;
        }

        $shop = Shop::findByDomain($host);
        if ($shop && $shop['is_active']) {
            $this->shopContext = $shop;
            return true;
        }

        return false;
    }

    private function matchRoute(string $method, string $uri, array $routes): ?array
    {
        $uri = parse_url($uri, PHP_URL_PATH);
        $uri = rtrim($uri, '/') ?: '/';

        if (!isset($routes[$method])) return null;

        foreach ($routes[$method] as $pattern => $handler) {
            $regex = preg_replace('/\{(\w+)\}/', '(?P<$1>[^/]+)', $pattern);
            $regex = '#^' . $regex . '$#';

            if (preg_match($regex, $uri, $matches)) {
                $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
                return [
                    'handler' => $handler,
                    'params' => $params,
                ];
            }
        }

        return null;
    }

    private function executeHandler(array $match): void
    {
        [$class, $method] = $match['handler'];
        $params = $match['params'];

        $middleware = $this->resolveMiddleware($class, $method);

        foreach ($middleware as $mw) {
            $mwInstance = new $mw($this->request);
            if (!$mwInstance->handle()) {
                return;
            }
        }

        $controller = new $class($this->request);
        echo call_user_func_array([$controller, $method], $params);
    }

    private function resolveMiddleware(string $controllerClass, string $method): array
    {
        $middleware = [];
        if (is_subclass_of($controllerClass, 'App\\Controllers\\AuthenticatedController')) {
            $middleware[] = 'App\\Middleware\\AuthMiddleware';
        }
        return $middleware;
    }

    private function notFound(): void
    {
        http_response_code(404);
        echo View::render('errors/404', [], true, 'admin');
    }
}
