<?php

namespace App\Controllers;

use App\Core\App;
use App\Core\Request;
use App\Core\View;

class DatabaseManagerController
{
    protected Request $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    private function requireAdmin(): void
    {
        if (!$this->request->hasSession('admin_id')) {
            View::redirect('/admin/login');
            exit;
        }
    }

    public function index(): string
    {
        $this->requireAdmin();

        $tables = $this->getTables();
        $selectedTable = $this->request->query('table', '');

        $columns = [];
        $indexes = [];
        $rows = [];
        $rowCount = 0;
        $page = 1;
        $totalPages = 1;
        $sql = '';
        $sqlError = '';

        if (!empty($selectedTable) && $this->tableExists($selectedTable)) {
            $columns = $this->getColumns($selectedTable);
            $indexes = $this->getIndexes($selectedTable);
            $page = max((int) $this->request->query('page', '1'), 1);
            $limit = 50;
            $offset = ($page - 1) * $limit;
            $rowCount = $this->getRowCount($selectedTable);
            $totalPages = max((int) ceil($rowCount / $limit), 1);
            $rows = $this->getRows($selectedTable, $limit, $offset);
        }

        return View::render('system/database', [
            'title' => 'مدیریت دیتابیس',
            'tables' => $tables,
            'selectedTable' => $selectedTable,
            'columns' => $columns,
            'indexes' => $indexes,
            'rows' => $rows,
            'rowCount' => $rowCount,
            'page' => $page,
            'totalPages' => $totalPages,
            'sql' => $sql,
            'sqlError' => $sqlError,
        ], true, 'admin');
    }

    public function query(): string
    {
        $this->requireAdmin();

        $sql = trim($this->request->input('sql', ''));
        $tables = $this->getTables();
        $result = [];
        $columns = [];
        $sqlError = '';
        $affected = 0;

        if (!empty($sql)) {
            $lowerSql = strtolower($sql);
            $isSelect = str_starts_with($lowerSql, 'select') || str_starts_with($lowerSql, 'with');
            $isModify = preg_match('/^\s*(insert|update|delete|drop|alter|create|truncate)/i', $sql);

            if ($isModify && !str_starts_with($lowerSql, 'select')) {
                try {
                    $stmt = App::db()->pdo()->prepare($sql);
                    $stmt->execute();
                    $affected = $stmt->rowCount();
                } catch (\Throwable $e) {
                    $sqlError = $e->getMessage();
                }
            } elseif ($isSelect) {
                try {
                    $stmt = App::db()->pdo()->prepare($sql);
                    $stmt->execute();
                    if ($stmt->columnCount() > 0) {
                        $result = $stmt->fetchAll(\PDO::FETCH_ASSOC);
                        for ($i = 0; $i < $stmt->columnCount(); $i++) {
                            $meta = $stmt->getColumnMeta($i);
                            $columns[] = $meta['name'] ?? "col_{$i}";
                        }
                    }
                } catch (\Throwable $e) {
                    $sqlError = $e->getMessage();
                }
            } else {
                $sqlError = 'فقط دستورات SELECT و دستورات ویرایشی پشتیبانی می‌شوند';
            }
        }

        return View::render('system/database', [
            'title' => 'مدیریت دیتابیس',
            'tables' => $tables,
            'selectedTable' => '',
            'columns' => [],
            'indexes' => [],
            'rows' => [],
            'rowCount' => 0,
            'page' => 1,
            'totalPages' => 1,
            'sql' => $sql,
            'sqlError' => $sqlError,
            'queryResult' => $result,
            'queryColumns' => $columns,
            'affected' => $affected,
        ], true, 'admin');
    }

    private function getTables(): array
    {
        return App::db()->fetchAll("
            SELECT table_name, 
                   (SELECT COUNT(*) FROM information_schema.columns WHERE table_name = t.table_name) as column_count,
                   pg_size_pretty(pg_total_relation_size(quote_ident(table_name))) as size
            FROM information_schema.tables t
            WHERE table_schema = 'public' AND table_type = 'BASE TABLE'
            ORDER BY table_name
        ");
    }

    private function tableExists(string $name): bool
    {
        $row = App::db()->fetch(
            "SELECT 1 FROM information_schema.tables WHERE table_schema = 'public' AND table_name = ?",
            [$name]
        );
        return (bool) $row;
    }

    private function getColumns(string $table): array
    {
        return App::db()->fetchAll("
            SELECT 
                column_name,
                data_type,
                character_maximum_length,
                is_nullable,
                column_default,
                (SELECT pgd.description FROM pg_catalog.pg_statio_all_tables as st
                 INNER JOIN pg_catalog.pg_description pgd ON pgd.objoid = st.relid
                 AND pgd.objsubid = c.ordinal_position
                 WHERE st.schemaname = 'public' AND st.relname = ?) as description
            FROM information_schema.columns c
            WHERE table_schema = 'public' AND table_name = ?
            ORDER BY ordinal_position
        ", [$table, $table]);
    }

    private function getIndexes(string $table): array
    {
        return App::db()->fetchAll("
            SELECT
                i.indexrelid::regclass as index_name,
                a.attname as column_name,
                i.indisunique as is_unique,
                i.indisprimary as is_primary
            FROM pg_index i
            JOIN pg_attribute a ON a.attrelid = i.indrelid AND a.attnum = ANY(i.indkey)
            WHERE i.indrelid = ?::regclass
            ORDER BY i.indexrelid::text, a.attnum
        ", [$table]);
    }

    private function getRowCount(string $table): int
    {
        $row = App::db()->fetch("SELECT reltuples::bigint as cnt FROM pg_class WHERE oid = ?::regclass", [$table]);
        return (int) ($row['cnt'] ?? 0);
    }

    private function getRows(string $table, int $limit, int $offset): array
    {
        $sql = App::db()->pdo()->prepare(
            "SELECT * FROM " . '"' . $table . '"' . " ORDER BY created_at DESC NULLS LAST LIMIT :limit OFFSET :offset"
        );
        $sql->bindValue('limit', $limit, \PDO::PARAM_INT);
        $sql->bindValue('offset', $offset, \PDO::PARAM_INT);
        $sql->execute();
        return $sql->fetchAll(\PDO::FETCH_ASSOC);
    }
}
