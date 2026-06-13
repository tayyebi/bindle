<?php

namespace App\Services;

use App\Core\Request;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;

class CartService
{
    private Request $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function getOrCreateCart(string $shopId): array
    {
        $cartId = $this->request->cookie('cart_id');
        if ($cartId) {
            $cart = Cart::findById($cartId);
            if ($cart && $cart['shop_id'] === $shopId) {
                Cart::touch($cartId);
                return $cart;
            }
        }

        $cartId = Cart::create($shopId);
        $this->request->setCookie('cart_id', $cartId);
        $cart = Cart::findById($cartId);
        return $cart;
    }

    public function addItem(string $shopId, string $productUrl): array
    {
        $schemaParser = new SchemaParser();
        $productData = $schemaParser->parse($productUrl);

        if (!$productData) {
            return ['error' => 'امکان تشخیص محصول وجود ندارد'];
        }

        $productData['url'] = $productUrl;

        $productId = Product::createOrUpdate($shopId, $productData);
        $product = Product::findById($productId);
        if (!$product) {
            return ['error' => 'خطا در ذخیره محصول'];
        }

        $cart = $this->getOrCreateCart($shopId);
        CartItem::add($cart['id'], $productId, 1, (float) $product['price']);

        return ['success' => true, 'product' => $product];
    }

    public function getCartItems(string $cartId): array
    {
        return Cart::getItems($cartId);
    }

    public function getCartTotal(string $cartId): float
    {
        return Cart::getTotal($cartId);
    }

    public function getItemCount(string $cartId): int
    {
        $items = Cart::getItems($cartId);
        return array_sum(array_column($items, 'quantity'));
    }

    public function updateItemQuantity(string $itemId, int $quantity): void
    {
        if ($quantity <= 0) {
            CartItem::remove($itemId);
        } else {
            CartItem::updateQuantity($itemId, $quantity);
        }
    }

    public function removeItem(string $itemId): void
    {
        CartItem::remove($itemId);
    }

    public function clearCart(string $cartId): void
    {
        CartItem::removeByCart($cartId);
    }
}
