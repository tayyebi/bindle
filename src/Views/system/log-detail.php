<?php $title = 'جزئیات لاگ'; ?>
<div class="log-detail-page">
    <div class="page-header">
        <h1>جزئیات لاگ</h1>
        <a href="/system/logs" class="btn btn-outline btn-sm">بازگشت</a>
    </div>

    <div class="detail-card">
        <div class="detail-row">
            <span class="detail-label">سطح</span>
            <span class="badge badge-<?= e($log['level']) ?>"><?= e($log['level']) ?></span>
        </div>
        <div class="detail-row">
            <span class="detail-label">پیام</span>
            <pre class="detail-value"><?= e($log['message']) ?></pre>
        </div>
        <div class="detail-row">
            <span class="detail-label">فایل</span>
            <code class="detail-value"><?= e($log['file']) ?></code>
        </div>
        <div class="detail-row">
            <span class="detail-label">خط</span>
            <span class="detail-value"><?= pnum($log['line']) ?></span>
        </div>
        <?php if (!empty($log['trace'])): ?>
            <div class="detail-row">
                <span class="detail-label">ردیابی</span>
                <pre class="detail-value trace"><?= e($log['trace']) ?></pre>
            </div>
        <?php endif; ?>
        <div class="detail-row">
            <span class="detail-label">کاربر</span>
            <span class="detail-value">
                <?= $log['user_type'] ? e($log['user_type']) . ': ' . e($log['user_id']) : '—' ?>
            </span>
        </div>
        <div class="detail-row">
            <span class="detail-label">درخواست</span>
            <span class="detail-value"><?= e($log['request_method']) ?> <?= e($log['request_url']) ?></span>
        </div>
        <div class="detail-row">
            <span class="detail-label">IP</span>
            <span class="detail-value"><?= e($log['request_ip']) ?></span>
        </div>
        <div class="detail-row">
            <span class="detail-label">زمان</span>
            <span class="detail-value"><?= e($log['created_at']) ?></span>
        </div>
    </div>
</div>
