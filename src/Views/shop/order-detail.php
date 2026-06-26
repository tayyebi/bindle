<?php $title = trans('orders'); ?>
<div class="order-detail-page">
    <a href="/dashboard/orders" class="btn btn-sm btn-outline"><?= trans('back') ?></a>
    <h1><?= trans('order_number') ?>: <?= e($order['token']) ?></h1>

    <div class="order-card">
        <div class="order-header">
            <span class="badge badge-<?= e($order['status']) ?>"><?= trans($order['status']) ?></span>
            <span class="text-gray"><?= e($order['created_at']) ?></span>
        </div>

        <div class="order-details">
            <h3><?= trans('customer_info') ?></h3>
            <div class="detail-row"><span><?= trans('full_name') ?>:</span><span><?= e($order['customer_name']) ?></span></div>
            <div class="detail-row"><span><?= trans('email') ?>:</span><span><?= e($order['customer_email']) ?></span></div>
            <?php if (!empty($order['customer_phone'])): ?>
                <div class="detail-row"><span>تلفن:</span><span dir="ltr"><?= e($order['customer_phone']) ?></span></div>
            <?php endif; ?>
            <?php if ($order['shipping_address']): ?>
                <div class="detail-row"><span><?= trans('shipping_address') ?>:</span><span><?= nl2br(e($order['shipping_address'])) ?></span></div>
            <?php endif; ?>
            <?php if ($order['notes']): ?>
                <div class="detail-row"><span>یادداشت:</span><span><?= nl2br(e($order['notes'])) ?></span></div>
            <?php endif; ?>
        </div>

        <div class="order-products">
            <h3><?= trans('products') ?></h3>
            <div class="table-wrapper">
                <table class="table">
                    <thead>
                        <tr>
                            <th><?= trans('product') ?></th>
                            <th><?= trans('price') ?></th>
                            <th><?= trans('quantity') ?></th>
                            <th><?= trans('total') ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($products as $product): ?>
                            <tr>
                                <td><?= e($product['name']) ?></td>
                                <td><?= pnum(number_format((float) $product['price_at_add'], 0)) ?></td>
                                <td><?= pnum($product['quantity']) ?></td>
                                <td><?= pnum(number_format((float) ($product['quantity'] * $product['price_at_add']), 0)) ?> <?= e($order['currency']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <div class="order-total">
                <strong><?= trans('total') ?>: <?= pnum(number_format((float) $order['total'], 0)) ?> <?= e($order['currency']) ?></strong>
            </div>
        </div>

        <?php if (!empty($proofs)): ?>
            <div class="payment-proofs">
                <h3><?= trans('upload_proof') ?></h3>
                <?php foreach ($proofs as $proof): ?>
                    <div class="proof-item">
                        <?php if ($proof['type'] === 'screenshot'): ?>
                            <img src="/<?= e($proof['value']) ?>" alt="proof" class="proof-image">
                        <?php else: ?>
                            <p><?= trans('proof_transaction_id') ?>: <?= e($proof['value']) ?></p>
                        <?php endif; ?>
                        <span class="text-gray"><?= e($proof['submitted_at']) ?></span>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <?php if ($order['status'] === 'pending'): ?>
            <div class="order-actions">
                <form method="POST" action="/dashboard/orders/<?= e($order['id']) ?>/approve" style="display:inline">
                    <button type="submit" class="btn btn-success"><?= trans('approve') ?></button>
                </form>
                <form method="POST" action="/dashboard/orders/<?= e($order['id']) ?>/reject" style="display:inline">
                    <button type="submit" class="btn btn-danger"><?= trans('reject') ?></button>
                </form>
            </div>
        <?php endif; ?>
    </div>
</div>
