<?php

namespace App\Models;

use App\Core\App;

class PaymentProof
{
    public static function findByOrderId(string $orderId): array
    {
        return App::db()->fetchAll(
            'SELECT * FROM payment_proofs WHERE order_id = ? ORDER BY submitted_at DESC',
            [$orderId]
        );
    }

    public static function create(array $data): string
    {
        return App::db()->insert('payment_proofs', [
            'order_id' => $data['order_id'],
            'type' => $data['type'],
            'value' => $data['value'],
        ]);
    }
}
