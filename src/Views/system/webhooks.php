<?php $title = 'مدیریت وب‌هوک‌های سیستم'; ?>
<div class="webhooks-page">
    <div class="page-header">
        <h1>مدیریت وب‌هوک‌های سیستم</h1>
        <a href="/system/webhooks/create" class="btn btn-primary btn-sm">وب‌هوک جدید</a>
    </div>

    <?php if (empty($webhooks)): ?>
        <p class="text-gray">هیچ وب‌هوکی تعریف نشده است.</p>
    <?php else: ?>
        <div class="table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th>توضیحات</th>
                        <th>آدرس</th>
                        <th>رویدادها</th>
                        <th>وضعیت</th>
                        <th>عملیات</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($webhooks as $w): ?>
                        <tr>
                            <td><?= e($w['description'] ?: '—') ?></td>
                            <td><code><?= e(mb_substr($w['url'], 0, 50)) ?>…</code></td>
                            <td>
                                <?php foreach (explode(',', $w['events']) as $ev): ?>
                                    <span class="badge badge-info"><?= e(trim($ev)) ?></span>
                                <?php endforeach; ?>
                            </td>
                            <td>
                                <span class="badge badge-<?= $w['is_active'] ? 'success' : 'error' ?>">
                                    <?= $w['is_active'] ? 'فعال' : 'غیرفعال' ?>
                                </span>
                            </td>
                            <td class="actions-cell">
                                <a href="/system/webhooks/edit/<?= e($w['id']) ?>" class="btn btn-sm btn-outline">ویرایش</a>
                                <a href="/system/webhooks/toggle/<?= e($w['id']) ?>" class="btn btn-sm btn-outline">
                                    <?= $w['is_active'] ? 'غیرفعال' : 'فعال' ?>
                                </a>
                                <a href="/system/webhooks/test/<?= e($w['id']) ?>" class="btn btn-sm btn-outline">تست</a>
                                <a href="/system/webhooks/delete/<?= e($w['id']) ?>" class="btn btn-sm btn-outline"
                                   onclick="return confirm('حذف شود؟')">حذف</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>
