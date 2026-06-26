<!DOCTYPE html>
<html dir="rtl" lang="fa">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= trans('app.name') ?> | <?= $title ?? trans('dashboard') ?></title>
    <link rel="preconnect" href="https://cdn.jsdelivr.net">
    <link rel="preload" href="https://cdn.jsdelivr.net/gh/rastikerdar/vazirmatn@v33.003/Vazirmatn-font-face.css" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <noscript><link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/rastikerdar/vazirmatn@v33.003/Vazirmatn-font-face.css"></noscript>
    <link rel="stylesheet" href="/css/app.css?v=1">
</head>
<body>
    <header class="header">
        <div class="container">
            <a href="/" class="logo">
                <img src="/img/logo.svg" alt="<?= trans('app.name') ?>" width="32" height="32">
                <span><?= trans('app.name') ?></span>
            </a>
            <nav class="nav">
<?php
$items = [];
if (isset($shop) && $shop) {
    $items[] = ['/dashboard', trans('dashboard')];
    $items[] = ['/dashboard/orders', trans('orders')];
    $items[] = ['/dashboard/settings', trans('settings')];
    $items[] = ['https://' . e($shop['domain']), 'مشاهده فروشگاه', true];
    $items[] = ['/profile', 'پروفایل'];
    $items[] = ['/logout', trans('logout')];
} elseif (isset($_SESSION['admin_id'])) {
    $items[] = ['/admin', trans('dashboard')];
    $items[] = ['/admin/shops', trans('shops')];
    $items[] = ['/system/logs', 'لاگ‌ها'];
    $items[] = ['/system/webhooks', 'وب‌هوک'];
    $items[] = ['/system/database', 'دیتابیس'];
    $items[] = ['/profile', 'پروفایل'];
    $items[] = ['/logout', trans('logout')];
} else {
    $items[] = ['/login', trans('login')];
}
?>
                <?php if (!empty($_SESSION['impersonated_by_admin'])): ?>
                    <a href="/admin/unimpersonate" class="btn btn-sm btn-danger">بازگشت به مدیریت</a>
                <?php endif; ?>
                <?php foreach ($items as $it): ?>
                    <a href="<?= $it[0] ?>" class="btn btn-sm btn-outline"<?= !empty($it[2]) ? ' target="_blank" rel="noopener"' : '' ?>><?= $it[1] ?></a>
                <?php endforeach; ?>
            </nav>
        </div>
    </header>

    <main class="main">
        <div class="container">
            <?= $content ?>
        </div>
    </main>

    <footer class="footer">
        <div class="container">
            <p>&copy; <?= date('Y') ?> <?= trans('app.name') ?>. <?= trans('all_rights') ?>.</p>
        </div>
    </footer>

    <script src="/js/app.js"></script>
</body>
</html>
