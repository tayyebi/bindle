<div class="login-page">
    <div class="login-card">
        <div class="login-logo">
            <img src="/img/logo.svg" alt="<?= trans('app.name') ?>" width="48" height="48">
            <h2><?= trans('app.name') ?></h2>
        </div>

        <?php if (isset($error)): ?>
            <div class="alert alert-error"><?= e($error) ?></div>
        <?php endif; ?>

        <form method="POST" action="/login">
            <div class="form-group">
                <label for="email">ایمیل</label>
                <input type="text" id="email" name="email" required class="form-control"
                       placeholder="ایمیل فروشگاه یا نام کاربری مدیر">
            </div>

            <div class="form-group">
                <label for="password">رمز عبور</label>
                <input type="password" id="password" name="password" required class="form-control">
            </div>

            <div class="form-group">
                <label class="checkbox-label">
                    <input type="checkbox" name="is_admin" value="1">
                    ورود به عنوان مدیر سیستم
                </label>
            </div>

            <button type="submit" class="btn btn-primary btn-lg btn-block"><?= trans('login') ?></button>
        </form>
    </div>
</div>
