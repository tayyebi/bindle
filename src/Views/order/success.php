<div class="order-success">
    <div class="success-icon">&#10003;</div>
    <h1><?= trans('order_placed') ?></h1>
    <p class="text-gray"><?= trans('order_number') ?>: <?= e(substr($order['token'], 0, 12)) ?>...</p>

    <div class="order-card">
        <div class="order-details">
            <div class="detail-row">
                <span><?= trans('full_name') ?>:</span>
                <span><?= e($order['customer_name']) ?></span>
            </div>
            <div class="detail-row">
                <span><?= trans('email') ?>:</span>
                <span><?= e($order['customer_email']) ?></span>
            </div>
            <div class="detail-row">
                <span><?= trans('total') ?>:</span>
                <span><?= pnum(number_format((float) $order['total'], 0)) ?> <?= e($order['currency']) ?></span>
            </div>
            <div class="detail-row">
                <span><?= trans('order_status') ?>:</span>
                <span class="status-<?= e($order['status']) ?>"><?= trans($order['status']) ?></span>
            </div>
        </div>

        <?php if ($order['status'] === 'pending' && !empty($shop['payment_instructions'])): ?>
            <div class="payment-instructions">
                <h3><?= trans('payment_instructions') ?></h3>
                <p><?= nl2br(e($shop['payment_instructions'])) ?></p>
            </div>
        <?php endif; ?>

        <a href="/order/<?= e($order['token']) ?>" class="btn btn-primary">
            <?= trans('order_status') ?>
        </a>
    </div>
</div>
