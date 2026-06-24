<?php $title = 'تأیید دو مرحله‌ای'; ?>
<div class="totp-verify-page">
    <div class="login-card">
        <div class="login-logo">
            <img src="/img/logo.svg" alt="<?= trans('app.name') ?>" width="48" height="48">
            <h2><?= trans('app.name') ?></h2>
        </div>

        <h3>تأیید دو مرحله‌ای</h3>
        <p class="text-gray">لطفاً کد ۶ رقمی اپلیکیشن تأیید را وارد کنید:</p>

        <?php if (!empty($error)): ?>
            <div class="alert alert-error"><?= e($error) ?></div>
        <?php endif; ?>

        <form method="POST" action="/totp/verify">
            <div class="form-group">
                <input type="text" name="code" required class="form-control totp-code-input"
                       placeholder="۰۰۰۰۰۰" maxlength="6" inputmode="numeric" autocomplete="off">
            </div>
            <button type="submit" class="btn btn-primary btn-lg btn-block">تأیید</button>
        </form>
    </div>
</div>
