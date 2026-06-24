<?php $title = 'مدیریت لاگ‌ها'; ?>
<div class="logs-page">
    <div class="page-header">
        <h1>مدیریت لاگ‌ها</h1>
        <div class="page-actions">
            <a href="/system/logs/clear" class="btn btn-outline btn-sm"
               onclick="return confirm('همه لاگ‌ها پاک شوند؟')">پاک کردن لاگ‌ها</a>
        </div>
    </div>

    <div class="stats-grid stats-sm">
        <?php foreach ($levelCounts as $lc): ?>
            <div class="stat-card level-<?= e($lc['level']) ?>">
                <div class="stat-value"><?= pnum($lc['count']) ?></div>
                <div class="stat-label"><?= e($lc['level']) ?></div>
            </div>
        <?php endforeach; ?>
    </div>

    <form method="GET" action="/system/logs" class="filters-bar">
        <select name="level" class="form-control filter-select">
            <option value="">همه سطوح</option>
            <option value="critical" <?= $level === 'critical' ? 'selected' : '' ?>>Critical</option>
            <option value="error" <?= $level === 'error' ? 'selected' : '' ?>>Error</option>
            <option value="warning" <?= $level === 'warning' ? 'selected' : '' ?>>Warning</option>
            <option value="notice" <?= $level === 'notice' ? 'selected' : '' ?>>Notice</option>
            <option value="info" <?= $level === 'info' ? 'selected' : '' ?>>Info</option>
        </select>
        <input type="text" name="search" class="form-control filter-search" placeholder="جستجو در پیام..."
               value="<?= e($search) ?>">
        <select name="order" class="form-control filter-select">
            <option value="DESC" <?= $order === 'DESC' ? 'selected' : '' ?>>جدیدترین</option>
            <option value="ASC" <?= $order === 'ASC' ? 'selected' : '' ?>>قدیمی‌ترین</option>
        </select>
        <button type="submit" class="btn btn-primary btn-sm">فیلتر</button>
    </form>

    <div class="table-container">
        <table class="table">
            <thead>
                <tr>
                    <th>سطح</th>
                    <th>پیام</th>
                    <th>فایل</th>
                    <th>خط</th>
                    <th>کاربر</th>
                    <th>زمان</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($logs)): ?>
                    <tr><td colspan="6" class="text-center text-gray">هیچ لاگی یافت نشد</td></tr>
                <?php else: ?>
                    <?php foreach ($logs as $log): ?>
                        <tr class="log-row log-level-<?= e($log['level']) ?>">
                            <td>
                                <span class="badge badge-<?= e($log['level']) ?>"><?= e($log['level']) ?></span>
                            </td>
                            <td class="log-message">
                                <a href="/system/logs/<?= e($log['id']) ?>"><?= e(mb_substr($log['message'], 0, 120)) ?></a>
                            </td>
                            <td class="log-file"><?= e(basename($log['file'])) ?></td>
                            <td class="log-line"><?= pnum($log['line']) ?></td>
                            <td>
                                <?php if ($log['user_type']): ?>
                                    <small><?= e($log['user_type']) ?>: <?= e(mb_substr($log['user_id'], 0, 8)) ?>…</small>
                                <?php else: ?>
                                    <small class="text-gray">—</small>
                                <?php endif; ?>
                            </td>
                            <td class="log-time"><?= e($log['created_at']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <?php if ($totalPages > 1): ?>
        <div class="pagination">
            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <a href="/system/logs?page=<?= $i ?>&level=<?= e($level) ?>&search=<?= e($search) ?>&order=<?= e($order) ?>"
                   class="btn btn-sm <?= $i === $page ? 'btn-primary' : 'btn-outline' ?>">
                    <?= pnum($i) ?>
                </a>
            <?php endfor; ?>
        </div>
    <?php endif; ?>
</div>
