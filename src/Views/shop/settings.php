<?php $title = trans('settings'); ?>
<div class="settings-page">
    <h1><?= trans('shop_settings') ?></h1>

    <form method="POST" action="/dashboard/settings" class="settings-form">
        <div class="form-group">
            <label for="name"><?= trans('shop_name') ?></label>
            <input type="text" id="name" name="name" value="<?= e($shop['name']) ?>" required class="form-control">
        </div>

        <div class="form-group">
            <label for="domain"><?= trans('domain') ?></label>
            <input type="text" id="domain" value="<?= e($shop['domain']) ?>" class="form-control" disabled>
            <small class="text-gray">برای تغییر دامنه با مدیر سیستم تماس بگیرید</small>
        </div>

        <div class="form-group">
            <label for="email">ایمیل</label>
            <input type="email" id="email" value="<?= e($shop['email']) ?>" class="form-control" disabled>
        </div>

        <div class="form-group">
            <label for="payment_instructions"><?= trans('payment_instructions_label') ?></label>
            <textarea id="payment_instructions" name="payment_instructions" rows="5" class="form-control"><?= e($shop['payment_instructions']) ?></textarea>
            <small class="text-gray">این متن به مشتریان بعد از ثبت سفارش نمایش داده می‌شود</small>
        </div>

        <div class="form-group">
            <label for="webhook_url"><?= trans('webhook_url') ?></label>
            <input type="url" id="webhook_url" name="webhook_url" value="<?= e($shop['webhook_url']) ?>" class="form-control" placeholder="https://...">
            <small class="text-gray">رویدادهای سفارش به این آدرس ارسال می‌شوند (اختیاری)</small>
        </div>

        <fieldset class="form-group">
            <legend style="font-weight:600;font-size:0.9rem;margin-bottom:0.35rem">فیلدهای اختیاری در فرم سفارش</legend>
            <?php $enabled = explode(',', $shop['checkout_fields'] ?? 'email,phone'); ?>
            <label class="checkbox-label">
                <input type="checkbox" name="checkout_fields[]" value="email" <?= in_array('email', $enabled) ? 'checked' : '' ?>>
                ایمیل
            </label>
            <label class="checkbox-label">
                <input type="checkbox" name="checkout_fields[]" value="phone" <?= in_array('phone', $enabled) ? 'checked' : '' ?>>
                تلفن
            </label>
        </fieldset>

        <button type="submit" class="btn btn-primary"><?= trans('save') ?></button>
    </form>
</div>
