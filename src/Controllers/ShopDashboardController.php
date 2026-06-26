<?php

namespace App\Controllers;

use App\Core\Request;
use App\Core\View;
use App\Models\Order;
use App\Models\Shop;
use App\Services\WebhookService;

class ShopDashboardController
{
    protected Request $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    private function getShop(): ?array
    {
        $shopId = $this->request->session('shop_id');
        if (!$shopId) return null;
        return Shop::findById($shopId);
    }

    private function requireAuth(): ?array
    {
        $shop = $this->getShop();
        if (!$shop) {
            View::redirect('/login');
            return null;
        }
        return $shop;
    }

    public function index(): string
    {
        $shop = $this->requireAuth();
        if (!$shop) return '';

        $orders = Order::findByShopId($shop['id']);
        $pendingCount = count(array_filter($orders, fn($o) => $o['status'] === 'pending'));
        $approvedCount = count(array_filter($orders, fn($o) => $o['status'] === 'approved'));

        return View::render('shop/dashboard', [
            'shop' => $shop,
            'orders' => $orders,
            'pendingCount' => $pendingCount,
            'approvedCount' => $approvedCount,
            'totalOrders' => count($orders),
        ], true, 'admin');
    }

    public function orders(): string
    {
        $shop = $this->requireAuth();
        if (!$shop) return '';

        $status = $this->request->query('status', '');
        $orders = Order::findByShopId($shop['id'], $status);

        return View::render('shop/orders', [
            'shop' => $shop,
            'orders' => $orders,
            'currentStatus' => $status,
        ], true, 'admin');
    }

    public function orderDetail(string $id): string
    {
        $shop = $this->requireAuth();
        if (!$shop) return '';

        $order = Order::findById($id);
        if (!$order || $order['shop_id'] !== $shop['id']) {
            return View::render('errors/404', [], false);
        }

        $products = Order::getOrderProducts($order['id']);
        $proofs = \App\Models\PaymentProof::findByOrderId($order['id']);

        return View::render('shop/order-detail', [
            'shop' => $shop,
            'order' => $order,
            'products' => $products,
            'proofs' => $proofs,
        ], true, 'admin');
    }

    public function approveOrder(string $id): string
    {
        $shop = $this->requireAuth();
        if (!$shop) return '';

        $order = Order::findById($id);
        if ($order && $order['shop_id'] === $shop['id'] && $order['status'] === 'pending') {
            Order::approve($id);
            $order = Order::findById($id);
            WebhookService::dispatch($shop['id'], 'order.approved', $order);

            foreach (Order::getOrderProducts($order['id']) as $product) {
                if ($product['type'] === 'digital') {
                    \App\Models\DownloadToken::create($order['id'], $product['id']);
                }
            }
        }

        View::redirect('/dashboard/orders/' . $id);
        return '';
    }

    public function rejectOrder(string $id): string
    {
        $shop = $this->requireAuth();
        if (!$shop) return '';

        $order = Order::findById($id);
        if ($order && $order['shop_id'] === $shop['id'] && $order['status'] === 'pending') {
            Order::reject($id);
            $order = Order::findById($id);
            WebhookService::dispatch($shop['id'], 'order.rejected', $order);
        }

        View::redirect('/dashboard/orders/' . $id);
        return '';
    }

    public function settings(): string
    {
        $shop = $this->requireAuth();
        if (!$shop) return '';

        return View::render('shop/settings', [
            'shop' => $shop,
        ], true, 'admin');
    }

    public function updateSettings(): string
    {
        $shop = $this->requireAuth();
        if (!$shop) return '';

        $checkoutFields = $this->request->input('checkout_fields', []);
        Shop::update($shop['id'], [
            'name' => $this->request->input('name', $shop['name']),
            'payment_instructions' => $this->request->input('payment_instructions', ''),
            'webhook_url' => $this->request->input('webhook_url', ''),
            'checkout_fields' => is_array($checkoutFields) ? implode(',', $checkoutFields) : 'email,phone',
        ]);

        View::redirect('/dashboard/settings');
        return '';
    }
}
