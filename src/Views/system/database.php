<?php
$title = 'مدیریت دیتابیس';
$tab = $_GET['tab'] ?? ($selectedTable ? 'data' : 'query');
if (!in_array($tab, ['schema', 'indexes', 'data', 'query'], true)) $tab = 'data';
$isQueryResult = isset($queryResult);
?>
<div class="db-page db-fullwidth">
    <div class="page-header">
        <h1>مدیریت دیتابیس</h1>
        <span class="text-gray"><?= pnum(count($tables)) ?> جدول</span>
    </div>

    <div class="db-toolbar">
        <div class="db-table-select">
            <label for="table-select">جدول:</label>
            <select id="table-select" class="form-control" onchange="if(this.value) window.location='/system/database?table='+this.value">
                <option value="">— انتخاب جدول —</option>
                <?php foreach ($tables as $t): ?>
                    <option value="<?= e($t['table_name']) ?>" <?= $selectedTable === $t['table_name'] ? 'selected' : '' ?>>
                        <?= e($t['table_name']) ?> (<?= pnum($t['column_count']) ?>)
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="db-query-bar">
            <form method="POST" action="/system/database/query" class="db-query-form">
                <input type="text" name="sql" class="form-control db-query-input"
                       placeholder="SELECT * FROM shops LIMIT 10"
                       value="<?= e($sql) ?>" dir="ltr">
                <button type="submit" class="btn btn-primary btn-sm">اجرا</button>
            </form>
        </div>
    </div>

    <?php if ($selectedTable): ?>
        <div class="db-tabs">
            <a href="/system/database?table=<?= e($selectedTable) ?>&tab=data"
               class="db-tab <?= $tab === 'data' ? 'active' : '' ?>">داده‌ها</a>
            <a href="/system/database?table=<?= e($selectedTable) ?>&tab=schema"
               class="db-tab <?= $tab === 'schema' ? 'active' : '' ?>">ساختار</a>
            <a href="/system/database?table=<?= e($selectedTable) ?>&tab=indexes"
               class="db-tab <?= $tab === 'indexes' ? 'active' : '' ?>">ایندکس‌ها</a>
            <a href="/system/database?table=<?= e($selectedTable) ?>&tab=query"
               class="db-tab <?= $tab === 'query' ? 'active' : '' ?>">کوئری</a>
        </div>

        <div class="db-tab-content">
            <div class="db-table-info">
                <span class="text-gray"><?= pnum($rowCount) ?> رکورد</span>
            </div>

            <?php if ($tab === 'schema'): ?>
                <div class="table-container">
                    <table class="table table-sm table-striped">
                        <thead>
                            <tr>
                                <th>نام</th>
                                <th>نوع</th>
                                <th>طول</th>
                                <th>پیش‌فرض</th>
                                <th>Nullable</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($columns as $col): ?>
                                <tr>
                                    <td><code><?= e($col['column_name']) ?></code></td>
                                    <td><?= e($col['data_type']) ?></td>
                                    <td><?= $col['character_maximum_length'] ? pnum($col['character_maximum_length']) : '—' ?></td>
                                    <td><?= e($col['column_default'] ?? '—') ?></td>
                                    <td><?= $col['is_nullable'] === 'YES' ? 'بله' : 'خیر' ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

            <?php elseif ($tab === 'indexes'): ?>
                <?php if (empty($indexes)): ?>
                    <p class="text-gray">ایندکسی وجود ندارد.</p>
                <?php else: ?>
                    <div class="table-container">
                        <table class="table table-sm table-striped">
                            <thead>
                                <tr>
                                    <th>نام ایندکس</th>
                                    <th>ستون</th>
                                    <th>نوع</th>
                                </tr>
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

            <?php elseif ($tab === 'query'): ?>
                <div class="db-query-panel">
                    <form method="POST" action="/system/database/query" class="db-query-form-vert">
                        <textarea name="sql" class="form-control db-query-textarea" rows="6"
                                  placeholder="SELECT * FROM shops LIMIT 10" dir="ltr"><?= e($sql) ?></textarea>
                        <button type="submit" class="btn btn-primary">اجرا</button>
                    </form>
                </div>

            <?php else: ?>
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
                            <a href="/system/database?table=<?= e($selectedTable) ?>&tab=data&page=<?= $i ?>"
                               class="btn btn-sm <?= $i === $page ? 'btn-primary' : 'btn-outline' ?>">
                                <?= pnum($i) ?>
                            </a>
                        <?php endfor; ?>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>

    <?php elseif ($isQueryResult): ?>
        <div class="db-tabs">
            <span class="db-tab active">نتیجه کوئری</span>
        </div>

        <?php if ($sqlError): ?>
            <div class="alert alert-error"><?= e($sqlError) ?></div>
        <?php elseif ($affected > 0): ?>
            <div class="alert alert-success"><?= pnum($affected) ?> رکورد تحت تأثیر قرار گرفت</div>
            <pre class="sql-preview"><?= e($sql) ?></pre>
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
                                    <td><?php $v = $row[$col] ?? '—'; echo e(is_string($v) ? mb_substr($v, 0, 200) : (string) $v); ?></td>
                                <?php endforeach; ?>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <p class="text-gray">نتیجه‌ای یافت نشد.</p>
            <pre class="sql-preview"><?= e($sql) ?></pre>
        <?php endif; ?>

    <?php else: ?>
        <div class="db-welcome">
            <p class="text-gray">یک جدول از بالا انتخاب کنید یا یک کوئری SQL بنویسید.</p>
        </div>
    <?php endif; ?>
</div>
