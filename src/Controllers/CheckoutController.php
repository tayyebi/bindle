<?php

namespace App\Controllers;

use App\Core\Request;
use App\Core\View;
use App\Models\Order;
use App\Services\CartService;
use App\Services\WebhookService;

class CheckoutController
{
    protected Request $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function index(): string
    {
        $shop = $this->request->getShopContext();
        $cartService = new CartService($this->request);
        $cart = $cartService->getOrCreateCart($shop['id']);
        $items = $cartService->getCartItems($cart['id']);
        $total = $cartService->getCartTotal($cart['id']);

        if (empty($items)) {
            View::redirect('/cart');
            return '';
        }

        return View::render('checkout/index', [
            'shop' => $shop,
            'cart' => $cart,
            'items' => $items,
            'total' => $total,
        ]);
    }

    public function submit(): string
    {
        $shop = $this->request->getShopContext();
        $cartService = new CartService($this->request);
        $cart = $cartService->getOrCreateCart($shop['id']);
        $items = $cartService->getCartItems($cart['id']);
        $total = $cartService->getCartTotal($cart['id']);

        if (empty($items)) {
            View::redirect('/cart');
            return '';
        }

        $customerName = trim($this->request->input('customer_name', ''));
        $customerEmail = trim($this->request->input('customer_email', ''));
        $shippingAddress = trim($this->request->input('shipping_address', ''));
        $notes = trim($this->request->input('notes', ''));

        $errors = [];
        if (empty($customerName)) {
            $errors[] = 'نام و نام خانوادگی الزامی است';
        }
        if (!empty($customerEmail) && !filter_var($customerEmail, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'ایمیل وارد شده معتبر نیست';
        }

        $hasPhysical = false;
        foreach ($items as $item) {
            if ($item['type'] === 'physical') {
                $hasPhysical = true;
                break;
            }
        }

        if ($hasPhysical && empty($shippingAddress)) {
            $errors[] = 'آدرس تحویل برای محصولات فیزیکی الزامی است';
        }

        if (!empty($errors)) {
            return View::render('checkout/index', [
                'shop' => $shop,
                'cart' => $cart,
                'items' => $items,
                'total' => $total,
                'errors' => $errors,
                'old' => $_POST,
            ]);
        }

        $orderId = Order::create([
            'shop_id' => $shop['id'],
            'cart_id' => $cart['id'],
            'total' => $total,
            'currency' => $items[0]['currency'] ?? 'USD',
            'customer_name' => $customerName,
            'customer_email' => $customerEmail,
            'shipping_address' => $shippingAddress,
            'notes' => $notes,
        ]);

        $order = Order::findById($orderId);

        WebhookService::dispatch($shop['id'], 'order.created', $order);

        $this->request->removeCookie('cart_id');

        View::redirect('/order/' . $order['token'] . '/success');
        return '';
    }
}
