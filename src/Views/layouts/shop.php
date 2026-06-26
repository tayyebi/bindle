<!DOCTYPE html>
<html dir="rtl" lang="fa">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($shop['name'] ?? trans('app.name')) ?> | <?= trans('app.name') ?></title>
    <link rel="preconnect" href="https://cdn.jsdelivr.net">
    <link rel="preload" href="https://cdn.jsdelivr.net/gh/rastikerdar/vazirmatn@v33.003/Vazirmatn-font-face.css" as="style" onload="this.onload=null;this.rel='stylesheet'">
    <noscript><link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/rastikerdar/vazirmatn@v33.003/Vazirmatn-font-face.css"></noscript>
    <link rel="stylesheet" href="/css/app.css">
</head>
<body>
    <header class="header">
        <div class="container">
            <a href="/" class="logo">
                <img src="/img/logo.svg" alt="<?= trans('app.name') ?>" width="32" height="32">
                <span><?= e($shop['name'] ?? trans('app.name')) ?></span>
            </a>
            <nav class="nav">
                <a href="/cart" class="btn btn-sm btn-outline">
                    <?= trans('cart') ?>
                </a>
                <a href="/login" class="btn btn-sm btn-outline"><?= trans('login') ?></a>
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
