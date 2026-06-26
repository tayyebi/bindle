<div class="checkout-page">
    <h1><?= trans('checkout') ?></h1>

    <?php if (!empty($errors)): ?>
        <div class="alert alert-error">
            <ul>
                <?php foreach ($errors as $error): ?>
                    <li><?= e($error) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <div class="checkout-grid">
        <div class="checkout-form">
            <form method="POST" action="/checkout">
                <div class="form-group">
                    <label for="customer_name"><?= trans('full_name') ?></label>
                    <input type="text" id="customer_name" name="customer_name"
                           value="<?= e($old['customer_name'] ?? '') ?>"
                           required class="form-control">
                </div>

                <?php $checkoutFields = explode(',', $shop['checkout_fields'] ?? 'email,phone'); ?>
                <?php if (in_array('email', $checkoutFields)): ?>
                <div class="form-group">
                    <label for="customer_email"><?= trans('email') ?> <small class="text-gray">(اختیاری)</small></label>
                    <input type="email" id="customer_email" name="customer_email"
                           value="<?= e($old['customer_email'] ?? '') ?>"
                           class="form-control" placeholder="example@email.com">
                </div>
                <?php endif; ?>

                <?php if (in_array('phone', $checkoutFields)): ?>
                <div class="form-group">
                    <label for="customer_phone">تلفن <small class="text-gray">(اختیاری)</small></label>
                    <input type="tel" id="customer_phone" name="customer_phone"
                           value="<?= e($old['customer_phone'] ?? '') ?>"
                           class="form-control" placeholder="0912xxxxxxx">
                </div>
                <?php endif; ?>

                <div class="form-group">
                    <label for="shipping_address"><?= trans('shipping_address') ?></label>
                    <textarea id="shipping_address" name="shipping_address"
                              class="form-control"><?= e($old['shipping_address'] ?? '') ?></textarea>
                </div>

                <div class="form-group">
                    <label for="notes">یادداشت</label>
                    <textarea id="notes" name="notes"
                              class="form-control"><?= e($old['notes'] ?? '') ?></textarea>
                </div>

                <button type="submit" class="btn btn-primary btn-lg btn-block">
                    <?= trans('submit') ?>
                </button>
            </form>
        </div>

        <div class="checkout-summary">
            <h3><?= trans('order_summary') ?></h3>
            <div class="summary-items">
                <?php foreach ($items as $item): ?>
                    <div class="summary-item">
                        <span><?= e($item['name']) ?> × <?= pnum($item['quantity']) ?></span>
                        <span><?= pnum(number_format((float) ($item['quantity'] * $item['price_at_add']), 0)) ?></span>
                    </div>
                <?php endforeach; ?>
            </div>
            <div class="summary-total">
                <strong><?= trans('total') ?>:</strong>
                <strong><?= pnum(number_format($total, 0)) ?> <?= e($items[0]['currency'] ?? 'USD') ?></strong>
            </div>
        </div>
    </div>
</div>
