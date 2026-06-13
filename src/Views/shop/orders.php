<?php $title = trans('orders'); ?>
<div class="orders-page">
    <h1><?= trans('orders') ?></h1>

    <div class="filter-tabs">
        <a href="/dashboard/orders" class="btn btn-sm <?= empty($currentStatus) ? 'btn-primary' : 'btn-outline' ?>"><?= trans('orders') ?></a>
        <a href="/dashboard/orders?status=pending" class="btn btn-sm <?= $currentStatus === 'pending' ? 'btn-primary' : 'btn-outline' ?>"><?= trans('pending') ?></a>
        <a href="/dashboard/orders?status=approved" class="btn btn-sm <?= $currentStatus === 'approved' ? 'btn-primary' : 'btn-outline' ?>"><?= trans('approved') ?></a>
        <a href="/dashboard/orders?status=rejected" class="btn btn-sm <?= $currentStatus === 'rejected' ? 'btn-primary' : 'btn-outline' ?>"><?= trans('rejected') ?></a>
    </div>

    <?php if (empty($orders)): ?>
        <div class="empty-state">
            <p class="text-gray"><?= trans('no_orders') ?></p>
        </div>
    <?php else: ?>
        <div class="table-wrapper">
            <table class="table">
                <thead>
                    <tr>
                        <th><?= trans('order_number') ?></th>
                        <th><?= trans('customer') ?></th>
                        <th><?= trans('total') ?></th>
                        <th><?= trans('status') ?></th>
                        <th><?= trans('date') ?></th>
                        <th><?= trans('actions') ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($orders as $order): ?>
                        <tr>
                            <td class="dir-ltr"><?= e(substr($order['token'], 0, 8)) ?>...</td>
                            <td><?= e($order['customer_name']) ?></td>
                            <td><?= pnum(number_format((float) $order['total'], 0)) ?> <?= e($order['currency']) ?></td>
                            <td><span class="badge badge-<?= e($order['status']) ?>"><?= trans($order['status']) ?></span></td>
                            <td><?= e(date('Y-m-d', strtotime($order['created_at']))) ?></td>
                            <td><a href="/dashboard/orders/<?= e($order['id']) ?>" class="btn btn-sm btn-outline"><?= trans('actions') ?></a></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>
