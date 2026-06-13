<?php $title = trans('dashboard'); ?>
<div class="dashboard-page">
    <h1><?= trans('dashboard') ?></h1>
    <p class="text-gray"><?= e($shop['name']) ?></p>

    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-value"><?= pnum($totalOrders) ?></div>
            <div class="stat-label"><?= trans('orders') ?></div>
        </div>
        <div class="stat-card">
            <div class="stat-value"><?= pnum($pendingCount) ?></div>
            <div class="stat-label"><?= trans('pending') ?></div>
        </div>
        <div class="stat-card">
            <div class="stat-value"><?= pnum($approvedCount) ?></div>
            <div class="stat-label"><?= trans('approved') ?></div>
        </div>
    </div>

    <div class="dashboard-actions">
        <a href="/dashboard/orders" class="btn btn-primary"><?= trans('orders') ?></a>
        <a href="/dashboard/settings" class="btn btn-outline"><?= trans('settings') ?></a>
    </div>

    <?php if ($pendingCount > 0): ?>
        <div class="alert alert-info">
            شما <?= pnum($pendingCount) ?> سفارش در انتظار تأیید دارید.
        </div>
    <?php endif; ?>
</div>
