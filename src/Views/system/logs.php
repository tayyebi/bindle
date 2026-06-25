<?php $title = 'مدیریت لاگ‌ها'; ?>
<div class="logs-page">
    <div class="page-header">
        <h1>مدیریت لاگ‌ها</h1>
        <div class="page-actions">
            <a href="/system/logs/export?tab=<?= e($activeTab) ?>" class="btn btn-outline btn-sm">خروجی CSV</a>
            <a href="/system/logs/clear?tab=<?= e($activeTab) ?>" class="btn btn-outline btn-sm"
               onclick="return confirm('همه لاگ‌های این بخش پاک شوند؟')">پاک کردن</a>
        </div>
    </div>

    <div class="log-tabs">
        <a href="?tab=errors" class="log-tab <?= $activeTab === 'errors' ? 'active' : '' ?>">
            خطاها
            <span class="log-tab-badge"><?= pnum($tabStats['errors']) ?></span>
        </a>
        <a href="?tab=crawls" class="log-tab <?= $activeTab === 'crawls' ? 'active' : '' ?>">
            خزش‌ها
            <span class="log-tab-badge"><?= pnum($tabStats['crawls']) ?></span>
        </a>
        <a href="?tab=requests" class="log-tab <?= $activeTab === 'requests' ? 'active' : '' ?>">
            درخواست‌ها
            <span class="log-tab-badge"><?= pnum($tabStats['requests']) ?></span>
        </a>
    </div>

    <?php if ($activeTab === 'errors' && !empty($levelCounts)): ?>
        <div class="stats-grid stats-sm">
            <?php foreach ($levelCounts as $lc): ?>
                <div class="stat-card level-<?= e($lc['level']) ?>">
                    <div class="stat-value"><?= pnum($lc['count']) ?></div>
                    <div class="stat-label"><?= e($lc['level']) ?></div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php elseif ($activeTab === 'crawls' && $crawlStats): ?>
        <div class="stats-grid stats-sm">
            <div class="stat-card">
                <div class="stat-value"><?= pnum($crawlStats['total']) ?></div>
                <div class="stat-label">کل</div>
            </div>
            <div class="stat-card">
                <div class="stat-value" style="color:#2e7d32"><?= pnum($crawlStats['success']) ?></div>
                <div class="stat-label">موفق</div>
            </div>
            <div class="stat-card">
                <div class="stat-value" style="color:#c62828"><?= pnum($crawlStats['failed']) ?></div>
                <div class="stat-label">ناموفق</div>
            </div>
            <div class="stat-card">
                <div class="stat-value" style="color:#1565c0"><?= $crawlStats['total'] > 0 ? round($crawlStats['success'] / $crawlStats['total'] * 100) . '%' : '—' ?></div>
                <div class="stat-label">نرخ موفقیت</div>
            </div>
        </div>
    <?php elseif ($activeTab === 'requests' && $requestStats): ?>
        <div class="stats-grid stats-sm">
            <div class="stat-card">
                <div class="stat-value"><?= pnum($requestStats['total']) ?></div>
                <div class="stat-label">کل درخواست‌ها</div>
            </div>
            <?php foreach ($requestStats['by_method'] as $m): ?>
                <div class="stat-card">
                    <div class="stat-value"><?= pnum($m['count']) ?></div>
                    <div class="stat-label"><?= e($m['method']) ?></div>
                </div>
            <?php endforeach; ?>
            <?php foreach ($requestStats['by_status'] as $s): ?>
                <div class="stat-card">
                    <div class="stat-value"><?= pnum($s['count']) ?></div>
                    <div class="stat-label"><?= e($s['group_code']) ?>xx</div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <form method="GET" action="/system/logs" class="filters-bar">
        <input type="hidden" name="tab" value="<?= e($activeTab) ?>">

        <?php if ($activeTab === 'errors'): ?>
            <select name="level" class="form-control filter-select">
                <option value="">همه سطوح</option>
                <option value="critical" <?= ($level ?? '') === 'critical' ? 'selected' : '' ?>>Critical</option>
                <option value="error" <?= ($level ?? '') === 'error' ? 'selected' : '' ?>>Error</option>
                <option value="warning" <?= ($level ?? '') === 'warning' ? 'selected' : '' ?>>Warning</option>
                <option value="notice" <?= ($level ?? '') === 'notice' ? 'selected' : '' ?>>Notice</option>
                <option value="info" <?= ($level ?? '') === 'info' ? 'selected' : '' ?>>Info</option>
            </select>
        <?php elseif ($activeTab === 'crawls'): ?>
            <select name="success" class="form-control filter-select">
                <option value="">همه</option>
                <option value="1" <?= ($success ?? '') === '1' ? 'selected' : '' ?>>موفق</option>
                <option value="0" <?= ($success ?? '') === '0' ? 'selected' : '' ?>>ناموفق</option>
            </select>
            <select name="http_code" class="form-control filter-select">
                <option value="">همه کدها</option>
                <option value="200" <?= ($httpCode ?? '') === '200' ? 'selected' : '' ?>>200 OK</option>
                <option value="404" <?= ($httpCode ?? '') === '404' ? 'selected' : '' ?>>404</option>
                <option value="500" <?= ($httpCode ?? '') === '500' ? 'selected' : '' ?>>500</option>
                <option value="0" <?= ($httpCode ?? '') === '0' ? 'selected' : '' ?>>اتصال ناموفق</option>
            </select>
        <?php elseif ($activeTab === 'requests'): ?>
            <select name="method" class="form-control filter-select">
                <option value="">همه روش‌ها</option>
                <option value="GET" <?= ($method ?? '') === 'GET' ? 'selected' : '' ?>>GET</option>
                <option value="POST" <?= ($method ?? '') === 'POST' ? 'selected' : '' ?>>POST</option>
            </select>
            <select name="status_code" class="form-control filter-select">
                <option value="">همه وضعیت‌ها</option>
                <option value="2" <?= ($statusCode ?? '') === '2' ? 'selected' : '' ?>>2xx موفق</option>
                <option value="3" <?= ($statusCode ?? '') === '3' ? 'selected' : '' ?>>3xx تغییرمسیر</option>
                <option value="4" <?= ($statusCode ?? '') === '4' ? 'selected' : '' ?>>4xx خطای مشتری</option>
                <option value="5" <?= ($statusCode ?? '') === '5' ? 'selected' : '' ?>>5xx خطای سرور</option>
            </select>
        <?php endif; ?>

        <input type="text" name="search" class="form-control filter-search"
               placeholder="جستجو..." value="<?= e($search ?? '') ?>">
        <select name="order" class="form-control filter-select">
            <option value="DESC" <?= ($order ?? 'DESC') === 'DESC' ? 'selected' : '' ?>>جدیدترین</option>
            <option value="ASC" <?= ($order ?? 'DESC') === 'ASC' ? 'selected' : '' ?>>قدیمی‌ترین</option>
        </select>
        <select name="per_page" class="form-control filter-select filter-per-page">
            <option value="25" <?= ($perPage ?? 50) == 25 ? 'selected' : '' ?>>25</option>
            <option value="50" <?= ($perPage ?? 50) == 50 ? 'selected' : '' ?>>50</option>
            <option value="100" <?= ($perPage ?? 50) == 100 ? 'selected' : '' ?>>100</option>
            <option value="200" <?= ($perPage ?? 50) == 200 ? 'selected' : '' ?>>200</option>
        </select>
        <button type="submit" class="btn btn-primary btn-sm">فیلتر</button>
    </form>

    <div class="table-container">
        <table class="table table-log">
            <thead>
                <?php if ($activeTab === 'errors'): ?>
                    <tr>
                        <th class="col-level">سطح</th>
                        <th class="col-message">پیام</th>
                        <th class="col-file">فایل</th>
                        <th class="col-line">خط</th>
                        <th class="col-user">کاربر</th>
                        <th class="col-time">زمان</th>
                    </tr>
                <?php elseif ($activeTab === 'crawls'): ?>
                    <tr>
                        <th class="col-status">وضعیت</th>
                        <th class="col-url">URL</th>
                        <th class="col-http">HTTP</th>
                        <th class="col-duration">مدت</th>
                        <th class="col-product">محصول</th>
                        <th class="col-time">زمان</th>
                    </tr>
                <?php elseif ($activeTab === 'requests'): ?>
                    <tr>
                        <th class="col-method">روش</th>
                        <th class="col-url">URL</th>
                        <th class="col-status">وضعیت</th>
                        <th class="col-duration">مدت</th>
                        <th class="col-host">دامنه</th>
                        <th class="col-time">زمان</th>
                    </tr>
                <?php endif; ?>
            </thead>
            <tbody>
                <?php if (empty($logs)): ?>
                    <tr><td colspan="6" class="text-center text-gray">هیچ لاگی یافت نشد</td></tr>
                <?php else: ?>
                    <?php if ($activeTab === 'errors'): ?>
                        <?php foreach ($logs as $log): ?>
                            <tr class="log-row log-row-error log-level-<?= e($log['level']) ?>"
                                onclick="window.location='/system/logs/<?= e($log['id']) ?>?tab=errors'">
                                <td><span class="badge badge-<?= e($log['level']) ?>"><?= e($log['level']) ?></span></td>
                                <td class="log-message"><?= e(mb_substr($log['message'], 0, 120)) ?></td>
                                <td class="log-file"><?= e(basename($log['file'])) ?></td>
                                <td class="log-line"><?= pnum($log['line']) ?></td>
                                <td><?= $log['user_type'] ? e($log['user_type']) . '…' : '<span class="text-gray">—</span>' ?></td>
                                <td class="log-time"><?= e($log['created_at']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php elseif ($activeTab === 'crawls'): ?>
                        <?php foreach ($logs as $log): ?>
                            <tr class="log-row <?= $log['success'] ? 'log-row-ok' : 'log-row-fail' ?>"
                                onclick="window.location='/system/logs/<?= e($log['id']) ?>?tab=crawls'">
                                <td>
                                    <?php if ($log['success']): ?>
                                        <span class="badge badge-success">موفق</span>
                                    <?php else: ?>
                                        <span class="badge badge-error">ناموفق</span>
                                    <?php endif; ?>
                                </td>
                                <td class="log-url" title="<?= e($log['url']) ?>"><?= e(mb_substr($log['url'], 0, 60)) ?>…</td>
                                <td>
                                    <span class="badge badge-http-<?= $log['http_code'] > 0 ? (int)($log['http_code'] / 100) : 0 ?>">
                                        <?= $log['http_code'] ? pnum($log['http_code']) : '—' ?>
                                    </span>
                                </td>
                                <td class="log-duration"><?= pnum($log['duration_ms']) ?>ms</td>
                                <td class="log-product"><?= e(mb_substr($log['product_name'], 0, 40)) ?: '<span class="text-gray">—</span>' ?></td>
                                <td class="log-time"><?= e($log['created_at']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php elseif ($activeTab === 'requests'): ?>
                        <?php foreach ($logs as $log): ?>
                            <tr class="log-row log-row-<?= strtolower($log['method']) ?>"
                                onclick="window.location='/system/logs/<?= e($log['id']) ?>?tab=requests'">
                                <td><span class="badge badge-method-<?= strtolower($log['method']) ?>"><?= e($log['method']) ?></span></td>
                                <td class="log-url" title="<?= e($log['url']) ?>"><?= e(mb_substr($log['url'], 0, 60)) ?>…</td>
                                <td>
                                    <span class="badge badge-http-<?= (int)($log['status_code'] / 100) ?>">
                                        <?= pnum($log['status_code']) ?>
                                    </span>
                                </td>
                                <td class="log-duration"><?= pnum($log['duration_ms']) ?>ms</td>
                                <td class="log-host"><?= e($log['shop_domain']) ?: e($log['host']) ?></td>
                                <td class="log-time"><?= e($log['created_at']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <div class="log-footer">
        <div class="log-total">مجموع: <?= pnum($total) ?></div>
        <?php if ($totalPages > 1): ?>
            <div class="pagination">
                <?php if ($page > 1): ?>
                    <a href="?tab=<?= e($activeTab) ?>&page=<?= $page - 1 ?>&per_page=<?= e($perPage) ?>&order=<?= e($order ?? 'DESC') ?>&search=<?= e($search ?? '') ?>"
                       class="btn btn-sm btn-outline">قبلی</a>
                <?php endif; ?>
                <?php
                $startPage = max(1, $page - 2);
                $endPage = min($totalPages, $page + 2);
                for ($i = $startPage; $i <= $endPage; $i++):
                ?>
                    <a href="?tab=<?= e($activeTab) ?>&page=<?= $i ?>&per_page=<?= e($perPage) ?>&order=<?= e($order ?? 'DESC') ?>&search=<?= e($search ?? '') ?>"
                       class="btn btn-sm <?= $i === $page ? 'btn-primary' : 'btn-outline' ?>">
                        <?= pnum($i) ?>
                    </a>
                <?php endfor; ?>
                <?php if ($page < $totalPages): ?>
                    <a href="?tab=<?= e($activeTab) ?>&page=<?= $page + 1 ?>&per_page=<?= e($perPage) ?>&order=<?= e($order ?? 'DESC') ?>&search=<?= e($search ?? '') ?>"
                       class="btn btn-sm btn-outline">بعدی</a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</div>
