<?php $title = $webhook ? 'ویرایش وب‌هوک' : 'ایجاد وب‌هوک جدید'; ?>
<div class="webhook-form-page">
    <div class="page-header">
        <h1><?= $title ?></h1>
        <a href="/system/webhooks" class="btn btn-outline btn-sm">بازگشت</a>
    </div>

    <?php if (isset($errors)): ?>
        <?php foreach ($errors as $err): ?>
            <div class="alert alert-error"><?= e($err) ?></div>
        <?php endforeach; ?>
    <?php endif; ?>

    <form method="POST" action="<?= $webhook ? '/system/webhooks/edit/' . e($webhook['id']) : '/system/webhooks/create' ?>"
          class="settings-form">
        <div class="form-group">
            <label for="url">آدرس وب‌هوک (URL)</label>
            <input type="url" id="url" name="url" required class="form-control"
                   value="<?= e($old['url'] ?? ($webhook['url'] ?? '')) ?>"
                   placeholder="https://example.com/webhook">
        </div>

        <div class="form-group">
            <label>رویدادها</label>
            <div class="checkbox-group">
                <?php
                $availableEvents = [
                    'error.critical' => 'خطاهای بحرانی (Critical)',
                    'error.error' => 'خطاها (Error)',
                    'error.warning' => 'هشدارها (Warning)',
                    'test' => 'رویداد تست',
                ];
                $selectedEvents = $webhook ? explode(',', $webhook['events']) : ($old['events'] ?? []);
                foreach ($availableEvents as $val => $label):
                ?>
                    <label class="checkbox-label">
                        <input type="checkbox" name="events[]" value="<?= $val ?>"
                            <?= in_array($val, $selectedEvents) ? 'checked' : '' ?>>
                        <?= e($label) ?>
                    </label>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="form-group">
            <label class="checkbox-label">
                <input type="checkbox" name="is_active" value="1"
                    <?= (isset($webhook) && !$webhook['is_active']) ? '' : 'checked' ?>>
                فعال
            </label>
        </div>

        <div class="form-group">
            <label for="description">توضیحات</label>
            <input type="text" id="description" name="description" class="form-control"
                   value="<?= e($old['description'] ?? ($webhook['description'] ?? '')) ?>"
                   placeholder="مثال: ارسال خطاها به Slack">
        </div>

        <button type="submit" class="btn btn-primary">ذخیره</button>
    </form>
</div>
