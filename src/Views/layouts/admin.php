<!DOCTYPE html>
<html dir="rtl" lang="fa">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= trans('app.name') ?> | <?= $title ?? trans('dashboard') ?></title>
    <link rel="preconnect" href="https://cdn.jsdelivr.net">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/rastikerdar/sahel-font@v1.0.0/dist/Sahel.css">
    <link rel="stylesheet" href="/css/app.css">
</head>
<body>
    <header class="header">
        <div class="container">
            <a href="/" class="logo">
                <img src="/img/logo.svg" alt="<?= trans('app.name') ?>" width="32" height="32">
                <span><?= trans('app.name') ?></span>
            </a>
            <nav class="nav">
                <?php if (isset($shop) && $shop): ?>
                    <a href="/dashboard" class="btn btn-sm btn-outline"><?= trans('dashboard') ?></a>
                    <a href="/dashboard/orders" class="btn btn-sm btn-outline"><?= trans('orders') ?></a>
                    <a href="/dashboard/settings" class="btn btn-sm btn-outline"><?= trans('settings') ?></a>
                    <a href="/logout" class="btn btn-sm btn-outline"><?= trans('logout') ?></a>
                <?php elseif (isset($_SESSION['admin_id'])): ?>
                    <a href="/admin" class="btn btn-sm btn-outline"><?= trans('dashboard') ?></a>
                    <a href="/admin/shops" class="btn btn-sm btn-outline"><?= trans('shops') ?></a>
                    <a href="/logout" class="btn btn-sm btn-outline"><?= trans('logout') ?></a>
                <?php else: ?>
                    <a href="/login" class="btn btn-sm btn-outline"><?= trans('login') ?></a>
                <?php endif; ?>
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
