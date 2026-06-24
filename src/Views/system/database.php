<?php $title = 'مدیریت دیتابیس'; ?>
<div class="db-page">
    <div class="page-header">
        <h1>مدیریت دیتابیس</h1>
    </div>

    <div class="db-layout">
        <aside class="db-sidebar">
            <h3>جدول‌ها</h3>
            <ul class="db-tables">
                <?php foreach ($tables as $t): ?>
                    <li>
                        <a href="/system/database?table=<?= e($t['table_name']) ?>"
                           class="<?= $selectedTable === $t['table_name'] ? 'active' : '' ?>">
                            <?= e($t['table_name']) ?>
                            <small class="text-gray"><?= pnum($t['column_count']) ?> ستون</small>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>

            <h3>SQL</h3>
            <form method="POST" action="/system/database/query" class="sidebar-sql-form">
                <textarea name="sql" class="form-control sidebar-sql-input" rows="4" placeholder="SELECT * FROM shops LIMIT 10"><?= e($sql) ?></textarea>
                <button type="submit" class="btn btn-sm btn-primary btn-block" style="margin-top:0.5rem">اجرا</button>
            </form>
        </aside>

        <main class="db-main">
            <?php if (!empty($selectedTable) && !empty($columns)): ?>
                <div class="db-table-info">
                    <h2>جدول <?= e($selectedTable) ?></h2>
                    <span class="text-gray"><?= pnum($rowCount) ?> رکورد</span>
                </div>

                <h3>ساختار ستون‌ها</h3>
                <div class="table-container">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>نام</th>
                                <th>نوع</th>
                                <th>پیش‌فرض</th>
                                <th>Nullable</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($columns as $col): ?>
                                <tr>
                                    <td><code><?= e($col['column_name']) ?></code></td>
                                    <td><?= e($col['data_type']) ?>
                                        <?= $col['character_maximum_length'] ? '(' . pnum($col['character_maximum_length']) . ')' : '' ?>
                                    </td>
                                    <td><?= e($col['column_default'] ?? '—') ?></td>
                                    <td><?= $col['is_nullable'] === 'YES' ? 'بله' : 'خیر' ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <?php if (!empty($indexes)): ?>
                    <h3>ایندکس‌ها</h3>
                    <div class="table-container">
                        <table class="table table-sm">
                            <thead>
                                <tr><th>نام</th><th>ستون</th><th>ویژگی</th></tr>
                            </thead>
                            <tbody>
                                <?php foreach ($indexes as $idx): ?>
                                    <tr>
                                        <td><code><?= e($idx['index_name']) ?></code></td>
                                        <td><?= e($idx['column_name']) ?></td>
                                        <td>
                                            <?php if ($idx['is_primary']): ?>
                                                <span class="badge badge-primary">Primary</span>
                                            <?php elseif ($idx['is_unique']): ?>
                                                <span class="badge badge-info">Unique</span>
                                            <?php else: ?>
                                                <span class="badge">Index</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>

                <h3>داده‌ها</h3>
                <div class="table-container db-data">
                    <table class="table table-sm table-striped">
                        <thead>
                            <tr>
                                <?php foreach ($columns as $col): ?>
                                    <th><?= e($col['column_name']) ?></th>
                                <?php endforeach; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($rows)): ?>
                                <tr><td colspan="<?= count($columns) ?>" class="text-center text-gray">داده‌ای وجود ندارد</td></tr>
                            <?php else: ?>
                                <?php foreach ($rows as $row): ?>
                                    <tr>
                                        <?php foreach ($columns as $col): ?>
                                            <td class="cell-<?= e($col['data_type']) ?>">
                                                <?php
                                                    $val = $row[$col['column_name']] ?? '';
                                                    $str = is_array($val) ? json_encode($val, JSON_UNESCAPED_UNICODE) : (string) $val;
                                                    echo e(mb_substr($str, 0, 100));
                                                ?>
                                            </td>
                                        <?php endforeach; ?>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <?php if ($totalPages > 1): ?>
                    <div class="pagination">
                        <?php for ($i = 1; $i <= min($totalPages, 20); $i++): ?>
                            <a href="/system/database?table=<?= e($selectedTable) ?>&page=<?= $i ?>"
                               class="btn btn-sm <?= $i === $page ? 'btn-primary' : 'btn-outline' ?>">
                                <?= pnum($i) ?>
                            </a>
                        <?php endfor; ?>
                    </div>
                <?php endif; ?>

            <?php elseif (isset($queryResult)): ?>
                <h2>نتیجه کوئری</h2>
                <?php if ($sqlError): ?>
                    <div class="alert alert-error"><?= e($sqlError) ?></div>
                <?php elseif ($affected > 0): ?>
                    <div class="alert alert-success"><?= pnum($affected) ?> رکورد تحت تأثیر قرار گرفت</div>
                <?php elseif (!empty($queryResult)): ?>
                    <div class="alert alert-success"><?= pnum(count($queryResult)) ?> رکورد</div>
                    <div class="table-container">
                        <table class="table table-sm table-striped">
                            <thead>
                                <tr>
                                    <?php foreach ($queryColumns as $col): ?>
                                        <th><?= e($col) ?></th>
                                    <?php endforeach; ?>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($queryResult as $row): ?>
                                    <tr>
                                        <?php foreach ($queryColumns as $col): ?>
                                            <td><?php $v = $row[$col] ?? '—'; echo e(is_string($v) ? mb_substr($v, 0, 100) : (string) $v); ?></td>
                                        <?php endforeach; ?>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p class="text-gray">نتیجه‌ای یافت نشد.</p>
                <?php endif; ?>

                <form method="POST" action="/system/database/query" class="sql-form">
                    <textarea name="sql" class="form-control sql-input" rows="3" placeholder="SELECT * FROM shops LIMIT 10"><?= e($sql) ?></textarea>
                    <button type="submit" class="btn btn-primary btn-sm">اجرا</button>
                </form>

            <?php else: ?>
                <div class="db-welcome">
                    <p class="text-gray">یک جدول از سمت راست انتخاب کنید یا یک کوئری SQL بنویسید.</p>
                    <form method="POST" action="/system/database/query" class="sql-form" style="margin-top:1rem;max-width:600px;margin-left:auto;margin-right:auto">
                        <textarea name="sql" class="form-control sql-input" rows="3" placeholder="SELECT * FROM shops LIMIT 10"></textarea>
                        <button type="submit" class="btn btn-primary" style="margin-top:0.5rem">اجرا</button>
                    </form>
                </div>
            <?php endif; ?>
        </main>
    </div>
</div>
