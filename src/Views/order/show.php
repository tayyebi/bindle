<div class="order-page">
    <h1><?= trans('order_status') ?></h1>

    <div class="order-card">
        <div class="order-header">
            <div class="order-status status-<?= e($order['status']) ?>">
                <?= trans($order['status']) ?>
            </div>
            <span class="text-gray"><?= trans('order_number') ?>: <?= e(substr($order['token'], 0, 8)) ?>...</span>
        </div>

        <div class="order-details">
            <div class="detail-row">
                <span><?= trans('full_name') ?>:</span>
                <span><?= e($order['customer_name']) ?></span>
            </div>
            <div class="detail-row">
                <span><?= trans('email') ?>:</span>
                <span><?= e($order['customer_email']) ?></span>
            </div>
            <?php if ($order['shipping_address']): ?>
                <div class="detail-row">
                    <span><?= trans('shipping_address') ?>:</span>
                    <span><?= e($order['shipping_address']) ?></span>
                </div>
            <?php endif; ?>
            <div class="detail-row">
                <span><?= trans('total') ?>:</span>
                <span><?= pnum(number_format((float) $order['total'], 0)) ?> <?= e($order['currency']) ?></span>
            </div>
        </div>

        <div class="order-products">
            <h3><?= trans('products') ?></h3>
            <?php foreach ($products as $product): ?>
                <div class="order-product">
                    <span><?= e($product['name']) ?> × <?= pnum($product['quantity']) ?></span>
                    <span><?= pnum(number_format((float) ($product['quantity'] * $product['price_at_add']), 0)) ?></span>
                </div>
            <?php endforeach; ?>
        </div>

        <?php if ($order['status'] === 'pending'): ?>
            <div class="payment-section">
                <h3><?= trans('payment_instructions') ?></h3>
                <p><?= nl2br(e($shop['payment_instructions'] ?? '')) ?></p>

                <h3><?= trans('upload_proof') ?></h3>
                <form method="POST" action="/order/<?= e($order['id']) ?>/proof" enctype="multipart/form-data">
                    <div class="form-group">
                        <label>
                            <input type="radio" name="proof_type" value="screenshot" checked>
                            <?= trans('proof_screenshot') ?>
                        </label>
                        <input type="file" name="proof_file" class="form-control" accept="image/*">
                    </div>
                    <div class="form-group">
                        <label>
                            <input type="radio" name="proof_type" value="text">
                            <?= trans('proof_transaction_id') ?>
                        </label>
                        <textarea name="proof_text" class="form-control" placeholder="متن رسید" rows="3"></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary"><?= trans('submit') ?></button>
                </form>
            </div>
        <?php elseif ($order['status'] === 'approved'): ?>
            <div class="order-approved">
                <p>سفارش شما تأیید شد</p>
                <?php foreach ($products as $product): ?>
                    <?php if ($product['type'] === 'digital'): ?>
                        <a href="/download/<?= e($product['download_token'] ?? '') ?>" class="btn btn-primary">
                            <?= trans('download') ?> <?= e($product['name']) ?>
                        </a>
                    <?php endif; ?>
                <?php endforeach; ?>
            </div>
        <?php elseif ($order['status'] === 'rejected'): ?>
            <div class="order-rejected">
                <p>سفارش شما رد شد. لطفاً با فروشگاه تماس بگیرید.</p>
            </div>
        <?php endif; ?>
    </div>
</div>
