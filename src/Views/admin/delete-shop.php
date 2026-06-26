<?php $title = 'حذف فروشگاه'; ?>
<div class="delete-shop-page">
    <a href="/admin/shops" class="btn btn-sm btn-outline"><?= trans('back') ?></a>
    <h1>حذف فروشگاه</h1>

    <div class="detail-card" style="max-width:500px">
        <p>آیا از حذف فروشگاه زیر اطمینان دارید؟ این عملیات قابل بازگشت نیست.</p>

        <div class="detail-row">
            <span class="detail-label">نام</span>
            <span class="detail-value"><?= e($shop['name']) ?></span>
        </div>
        <div class="detail-row">
            <span class="detail-label">دامنه</span>
            <span class="detail-value dir-ltr"><?= e($shop['domain']) ?></span>
        </div>
        <div class="detail-row">
            <span class="detail-label">ایمیل</span>
            <span class="detail-value"><?= e($shop['email']) ?></span>
        </div>
        <div class="detail-row">
            <span class="detail-label">وضعیت</span>
            <span class="detail-value">غیرفعال</span>
        </div>

        <form method="POST" action="/admin/shops/<?= e($shop['id']) ?>/delete" style="margin-top:1.5rem">
            <button type="submit" class="btn btn-danger btn-lg">حذف فروشگاه</button>
        </form>
    </div>
</div>
