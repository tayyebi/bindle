<?php

namespace App\Controllers;

use App\Core\Request;
use App\Core\View;
use App\Services\CartService;

class CartController
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

        return View::render('cart/index', [
            'shop' => $shop,
            'cart' => $cart,
            'items' => $items,
            'total' => $total,
        ]);
    }

    public function add(): string
    {
        $shop = $this->request->getShopContext();
        $url = $this->request->query('url', '');

        if (empty($url)) {
            View::redirect('/cart');
            return '';
        }

        $cartService = new CartService($this->request);
        $result = $cartService->addItem($shop['id'], $url);

        if (isset($result['error'])) {
            $shop = $this->request->getShopContext();
            return View::render('cart/index', [
                'shop' => $shop,
                'error' => $result['error'],
                'items' => [],
                'total' => 0,
            ]);
        }

        View::redirect('/cart');
        return '';
    }

    public function update(): string
    {
        $itemId = $this->request->input('item_id');
        $quantity = (int) $this->request->input('quantity', 0);

        if ($itemId && $quantity > 0) {
            $cartService = new CartService($this->request);
            $cartService->updateItemQuantity($itemId, $quantity);
        }

        View::redirect('/cart');
        return '';
    }

    public function remove(): string
    {
        $itemId = $this->request->input('item_id');
        if ($itemId) {
            $cartService = new CartService($this->request);
            $cartService->removeItem($itemId);
        }

        View::redirect('/cart');
        return '';
    }
}
