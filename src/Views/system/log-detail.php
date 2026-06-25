<?php $title = 'جزئیات لاگ'; ?>
<div class="log-detail-page">
    <div class="page-header">
        <h1>جزئیات لاگ</h1>
        <a href="/system/logs?tab=<?= e($tab ?? 'errors') ?>" class="btn btn-outline btn-sm">بازگشت</a>
    </div>

    <?php if ($tab === 'crawls'): ?>
        <div class="detail-card">
            <div class="detail-row">
                <span class="detail-label">وضعیت</span>
                <span class="badge badge-<?= $log['success'] ? 'success' : 'error' ?>"><?= $log['success'] ? 'موفق' : 'ناموفق' ?></span>
            </div>
            <div class="detail-row">
                <span class="detail-label">URL</span>
                <code class="detail-value" style="direction:ltr;text-align:left;word-break:break-all"><?= e($log['url']) ?></code>
            </div>
            <div class="detail-row">
                <span class="detail-label">کد HTTP</span>
                <span class="badge badge-http-<?= $log['http_code'] > 0 ? (int)($log['http_code'] / 100) : 0 ?>"><?= pnum($log['http_code']) ?: '—' ?></span>
            </div>
            <div class="detail-row">
                <span class="detail-label">مدت زمان</span>
                <span class="detail-value"><?= pnum($log['duration_ms']) ?>ms</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">پارسر</span>
                <span class="detail-value"><?= e($log['parser_used']) ?: '—' ?></span>
            </div>
            <?php if (!empty($log['error_message'])): ?>
                <div class="detail-row">
                    <span class="detail-label">خطا</span>
                    <pre class="detail-value" style="color:#c62828"><?= e($log['error_message']) ?></pre>
                </div>
            <?php endif; ?>
            <?php if (!empty($log['product_name'])): ?>
                <div class="detail-row">
                    <span class="detail-label">محصول</span>
                    <span class="detail-value"><?= e($log['product_name']) ?></span>
                </div>
            <?php endif; ?>
            <?php if ($log['product_price'] !== null): ?>
                <div class="detail-row">
                    <span class="detail-label">قیمت</span>
                    <span class="detail-value"><?= pnum($log['product_price']) ?></span>
                </div>
            <?php endif; ?>
            <div class="detail-row">
                <span class="detail-label">زمان</span>
                <span class="detail-value"><?= e($log['created_at']) ?></span>
            </div>
        </div>

    <?php elseif ($tab === 'requests'): ?>
        <div class="detail-card">
            <div class="detail-row">
                <span class="detail-label">روش</span>
                <span class="badge badge-method-<?= strtolower($log['method']) ?>"><?= e($log['method']) ?></span>
            </div>
            <div class="detail-row">
                <span class="detail-label">URL</span>
                <code class="detail-value" style="direction:ltr;text-align:left;word-break:break-all"><?= e($log['url']) ?></code>
            </div>
            <div class="detail-row">
                <span class="detail-label">وضعیت</span>
                <span class="badge badge-http-<?= (int)($log['status_code'] / 100) ?>"><?= pnum($log['status_code']) ?></span>
            </div>
            <div class="detail-row">
                <span class="detail-label">مدت زمان</span>
                <span class="detail-value"><?= pnum($log['duration_ms']) ?>ms</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">دامنه</span>
                <span class="detail-value"><?= e($log['host']) ?></span>
            </div>
            <?php if (!empty($log['shop_domain'])): ?>
                <div class="detail-row">
                    <span class="detail-label">فروشگاه</span>
                    <span class="detail-value"><?= e($log['shop_domain']) ?></span>
                </div>
            <?php endif; ?>
            <div class="detail-row">
                <span class="detail-label">IP</span>
                <span class="detail-value"><?= e($log['ip']) ?: '—' ?></span>
            </div>
            <div class="detail-row">
                <span class="detail-label">User-Agent</span>
                <span class="detail-value" style="font-size:0.8rem;direction:ltr;text-align:left"><?= e($log['user_agent']) ?: '—' ?></span>
            </div>
            <div class="detail-row">
                <span class="detail-label">کاربر</span>
                <span class="detail-value"><?= $log['user_type'] ? e($log['user_type']) . ': ' . e($log['user_id']) : '—' ?></span>
            </div>
            <div class="detail-row">
                <span class="detail-label">زمان</span>
                <span class="detail-value"><?= e($log['created_at']) ?></span>
            </div>
        </div>

    <?php else: ?>
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
                <span class="detail-value"><?= $log['user_type'] ? e($log['user_type']) . ': ' . e($log['user_id']) : '—' ?></span>
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
    <?php endif; ?>
</div>
