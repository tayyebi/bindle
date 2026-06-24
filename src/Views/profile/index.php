<?php $title = 'پروفایل'; ?>
<div class="profile-page">
    <h1>پروفایل</h1>

    <?php if (isset($password_success)): ?>
        <div class="alert alert-success"><?= e($password_success) ?></div>
    <?php endif; ?>

    <?php if (isset($totp_success)): ?>
        <div class="alert alert-success"><?= e($totp_success) ?></div>
    <?php endif; ?>

    <div class="profile-section">
        <h2>تغییر رمز عبور</h2>
        <form method="POST" action="/profile/password" class="settings-form">
            <?php if (isset($password_errors)): ?>
                <?php foreach ($password_errors as $err): ?>
                    <div class="alert alert-error"><?= e($err) ?></div>
                <?php endforeach; ?>
            <?php endif; ?>

            <div class="form-group">
                <label for="current_password">رمز عبور فعلی</label>
                <input type="password" id="current_password" name="current_password" required class="form-control">
            </div>

            <div class="form-group">
                <label for="new_password">رمز عبور جدید</label>
                <input type="password" id="new_password" name="new_password" required class="form-control" minlength="6">
            </div>

            <div class="form-group">
                <label for="confirm_password">تکرار رمز عبور جدید</label>
                <input type="password" id="confirm_password" name="confirm_password" required class="form-control" minlength="6">
            </div>

            <button type="submit" class="btn btn-primary">تغییر رمز عبور</button>
        </form>
    </div>

    <div class="profile-section">
        <h2>تأیید دو مرحله‌ای (2FA)</h2>
        <?php if (!empty($user['totp_enabled'])): ?>
            <div class="alert alert-success">تأیید دو مرحله‌ای فعال است.</div>
            <form method="POST" action="/profile/totp/disable" style="display:inline">
                <button type="submit" class="btn btn-outline" onclick="return confirm('آیا از غیرفعال کردن تأیید دو مرحله‌ای اطمینان دارید؟')">غیرفعال کردن</button>
            </form>
        <?php else: ?>
            <p class="text-gray">تأیید دو مرحله‌ای غیرفعال است. با فعال کردن این قابلیت، امنیت حساب خود را افزایش دهید.</p>
            <a href="/profile/totp" class="btn btn-primary">فعال کردن تأیید دو مرحله‌ای</a>
        <?php endif; ?>
    </div>
</div>
