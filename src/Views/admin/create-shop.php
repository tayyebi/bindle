<?php $title = trans('create_shop'); ?>
<div class="create-shop-page">
    <a href="/admin/shops" class="btn btn-sm btn-outline"><?= trans('back') ?></a>
    <h1><?= trans('create_shop') ?></h1>

    <?php if (!empty($errors)): ?>
        <div class="alert alert-error">
            <ul>
                <?php foreach ($errors as $error): ?>
                    <li><?= e($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="POST" action="/admin/shops/create" class="shop-form">
        <div class="form-group">
            <label for="domain"><?= trans('domain') ?></label>
            <input type="text" id="domain" name="domain" required class="form-control"
                   placeholder="shop.example.com"
                   value="<?= e($old['domain'] ?? '') ?>">
            <small class="text-gray">دامنه‌ای که فروشگاه با CNAME به بقچه متصل می‌کند</small>
        </div>

        <div class="form-group">
            <label for="name"><?= trans('shop_name') ?></label>
            <input type="text" id="name" name="name" required class="form-control"
                   value="<?= e($old['name'] ?? '') ?>">
        </div>

        <div class="form-group">
            <label for="email">ایمیل</label>
            <input type="email" id="email" name="email" required class="form-control"
                   value="<?= e($old['email'] ?? '') ?>">
            <small class="text-gray">ایمیل ورود فروشنده به داشبورد</small>
        </div>

        <div class="form-group">
            <label for="password">رمز عبور</label>
            <input type="password" id="password" name="password" required class="form-control" minlength="6">
            <small class="text-gray">حداقل ۶ کاراکتر</small>
        </div>

        <button type="submit" class="btn btn-primary btn-lg"><?= trans('create_shop') ?></button>
    </form>
</div>
