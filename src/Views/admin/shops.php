<?php $title = trans('shops'); ?>
<div class="shops-page">
    <div class="page-header">
        <h1><?= trans('shops') ?></h1>
        <a href="/admin/shops/create" class="btn btn-primary"><?= trans('create_shop') ?></a>
    </div>

    <div class="table-wrapper">
        <table class="table">
            <thead>
                <tr>
                    <th><?= trans('shop_name') ?></th>
                    <th><?= trans('domain') ?></th>
                    <th>ایمیل</th>
                    <th><?= trans('is_active') ?></th>
                    <th><?= trans('date') ?></th>
                    <th><?= trans('actions') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($shops as $s): ?>
                    <tr>
                        <td><?= e($s['name']) ?></td>
                        <td class="dir-ltr"><a href="https://<?= e($s['domain']) ?>" target="_blank" rel="noopener"><?= e($s['domain']) ?></a></td>
                        <td><?= e($s['email']) ?></td>
                        <td><?= $s['is_active'] ? trans('yes') : trans('no') ?></td>
                        <td><?= e(date('Y-m-d', strtotime($s['created_at']))) ?></td>
                        <td>
                            <a href="/admin/shops/<?= e($s['id']) ?>/impersonate" class="btn btn-sm btn-primary">ورود</a>
                            <form method="POST" action="/admin/shops/<?= e($s['id']) ?>/toggle" style="display:inline">
                                <button type="submit" class="btn btn-sm btn-outline">
                                    <?= $s['is_active'] ? 'غیرفعال' : 'فعال' ?>
                                </button>
                            </form>
                            <?php if (!$s['is_active']): ?>
                                <a href="/admin/shops/<?= e($s['id']) ?>/delete" class="btn btn-sm btn-danger">حذف</a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
