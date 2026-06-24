<?php $title = 'تنظیمات تأیید دو مرحله‌ای'; ?>
<div class="totp-setup-page">
    <h1>تنظیمات تأیید دو مرحله‌ای</h1>

    <?php if (isset($error)): ?>
        <div class="alert alert-error"><?= e($error) ?></div>
    <?php endif; ?>

    <div class="totp-steps">
        <div class="totp-step">
            <h3>مرحله ۱: اپلیکیشن تأیید را نصب کنید</h3>
            <p class="text-gray">Google Authenticator، Authy یا هر اپلیکیشن TOTP دیگری را روی گوشی خود نصب کنید.</p>
        </div>

        <div class="totp-step">
            <h3>مرحله ۲: اسکن کنید یا کلید را وارد کنید</h3>
            <p class="text-gray">کد QR زیر را با اپلیکیشن اسکن کنید، یا کلید زیر را به صورت دستی وارد کنید:</p>
            <div class="totp-qr">
                <img src="<?= e($qr_url) ?>" alt="QR Code" width="200" height="200">
            </div>
            <div class="totp-secret">
                <label>کلید مخفی:</label>
                <code class="secret-key"><?= e($secret) ?></code>
            </div>
        </div>

        <div class="totp-step">
            <h3>مرحله ۳: کد تأیید را وارد کنید</h3>
            <p class="text-gray">کد ۶ رقمی تولید شده توسط اپلیکیشن را وارد کنید:</p>
            <form method="POST" action="/profile/totp/enable" class="totp-verify-form">
                <input type="hidden" name="secret" value="<?= e($secret) ?>">
                <div class="form-group">
                    <input type="text" name="code" required class="form-control totp-code-input"
                           placeholder="۰۰۰۰۰۰" maxlength="6" inputmode="numeric" autocomplete="off">
                </div>
                <button type="submit" class="btn btn-primary">فعال کردن</button>
                <a href="/profile" class="btn btn-outline">انصراف</a>
            </form>
        </div>
    </div>
</div>
