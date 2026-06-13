<div class="cart-page">
    <h1><?= trans('cart') ?></h1>

    <?php if (isset($error)): ?>
        <div class="alert alert-error"><?= e($error) ?></div>
    <?php endif; ?>

    <?php if (empty($items)): ?>
        <div class="empty-state">
            <p class="text-gray"><?= trans('empty_cart') ?></p>
            <a href="/" class="btn btn-primary"><?= trans('continue_shopping') ?></a>
        </div>
    <?php else: ?>
        <div class="cart-items">
            <?php foreach ($items as $item): ?>
                <div class="cart-item">
                    <div class="cart-item-image">
                        <?php if ($item['image_url']): ?>
                            <img src="<?= e($item['image_url']) ?>" alt="<?= e($item['name']) ?>">
                        <?php endif; ?>
                    </div>
                    <div class="cart-item-info">
                        <h3><?= e($item['name']) ?></h3>
                        <p class="text-gray"><?= pnum(number_format((float) $item['price_at_add'], 0)) ?> <?= e($item['currency']) ?></p>
                    </div>
                    <div class="cart-item-qty">
                        <form method="POST" action="/cart/update" class="qty-form">
                            <input type="hidden" name="item_id" value="<?= e($item['id']) ?>">
                            <button type="submit" name="quantity" value="<?= max(1, $item['quantity'] - 1) ?>" class="btn btn-sm btn-outline">-</button>
                            <span class="qty-value"><?= pnum($item['quantity']) ?></span>
                            <button type="submit" name="quantity" value="<?= $item['quantity'] + 1 ?>" class="btn btn-sm btn-outline">+</button>
                        </form>
                    </div>
                    <div class="cart-item-total">
                        <?= pnum(number_format((float) ($item['quantity'] * $item['price_at_add']), 0)) ?> <?= e($item['currency']) ?>
                    </div>
                    <div class="cart-item-remove">
                        <form method="POST" action="/cart/remove">
                            <input type="hidden" name="item_id" value="<?= e($item['id']) ?>">
                            <button type="submit" class="btn btn-sm btn-danger"><?= trans('remove') ?></button>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="cart-summary">
            <div class="cart-total">
                <span><?= trans('total') ?>:</span>
                <strong><?= pnum(number_format($total, 0)) ?> <?= e($items[0]['currency'] ?? 'USD') ?></strong>
            </div>
            <a href="/checkout" class="btn btn-primary btn-lg"><?= trans('checkout_now') ?></a>
        </div>
    <?php endif; ?>
</div>
