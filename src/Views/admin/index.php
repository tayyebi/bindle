<?php $title = trans('dashboard'); ?>
<div class="admin-page">
    <h1><?= trans('dashboard') ?></h1>
    <p class="text-gray"><?= trans('admins') ?></p>

    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-value"><?= pnum($totalShops) ?></div>
            <div class="stat-label"><?= trans('shops') ?></div>
        </div>
    </div>

    <div class="dashboard-actions">
        <a href="/admin/shops" class="btn btn-primary"><?= trans('shops') ?></a>
        <a href="/admin/shops/create" class="btn btn-outline"><?= trans('create_shop') ?></a>
    </div>
</div>
