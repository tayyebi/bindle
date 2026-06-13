<?php

namespace App\Controllers;

use App\Core\Request;
use App\Core\View;
use App\Models\DownloadToken;
use App\Models\Order;
use App\Models\PaymentProof;

class OrderController
{
    protected Request $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function show(string $token): string
    {
        $shop = $this->request->getShopContext();
        $order = Order::findByToken($token);

        if (!$order || $order['shop_id'] !== $shop['id']) {
            return View::render('errors/404', [], false);
        }

        $products = Order::getOrderProducts($order['id']);
        $proof = Order::getPaymentProof($order['id']);

        return View::render('order/show', [
            'shop' => $shop,
            'order' => $order,
            'products' => $products,
            'proof' => $proof,
        ]);
    }

    public function success(string $token): string
    {
        $order = Order::findByToken($token);
        if (!$order) {
            return View::render('errors/404', [], false);
        }

        $shop = null;
        if ($this->request->isShopContext()) {
            $shop = $this->request->getShopContext();
        } else {
            $shop = \App\Models\Shop::findById($order['shop_id']);
        }

        $products = Order::getOrderProducts($order['id']);

        return View::render('order/success', [
            'shop' => $shop,
            'order' => $order,
            'products' => $products,
        ], true, $shop ? 'shop' : 'admin');
    }

    public function submitProof(string $id): string
    {
        $shop = $this->request->getShopContext();
        $order = Order::findById($id);

        if (!$order || $order['shop_id'] !== $shop['id']) {
            View::redirect('/');
            return '';
        }

        if ($order['status'] !== 'pending') {
            View::redirect('/order/' . $order['token']);
            return '';
        }

        $type = $this->request->input('proof_type', 'text');
        $proofValue = '';

        if ($type === 'screenshot') {
            $file = $this->request->file('proof_file');
            if ($file) {
                $uploadDir = __DIR__ . '/../../storage/proofs';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }
                $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
                $filename = $order['id'] . '_' . time() . '.' . $ext;
                move_uploaded_file($file['tmp_name'], $uploadDir . '/' . $filename);
                $proofValue = 'storage/proofs/' . $filename;
            }
        } else {
            $proofValue = trim($this->request->input('proof_text', ''));
        }

        if (empty($proofValue)) {
            View::redirect('/order/' . $order['token']);
            return '';
        }

        PaymentProof::create([
            'order_id' => $id,
            'type' => $type,
            'value' => $proofValue,
        ]);

        View::redirect('/order/' . $order['token']);
        return '';
    }

    public function download(string $token): void
    {
        $dt = DownloadToken::findByToken($token);
        if (!$dt) {
            http_response_code(404);
            echo View::render('errors/404', [], false);
            return;
        }

        $product = \App\Models\Product::findById($dt['product_id']);
        if (!$product || empty($product['image_url'])) {
            http_response_code(404);
            echo 'فایل یافت نشد';
            return;
        }

        DownloadToken::markUsed($dt['id']);

        $fileUrl = $product['image_url'];
        header("Location: {$fileUrl}");
        exit;
    }
}
