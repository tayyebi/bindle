<?php

namespace App\Controllers;

use App\Core\App;
use App\Core\Request;
use App\Core\View;
use App\Services\Logger;

class SystemLogController
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

        $level = $this->request->query('level', '');
        $search = $this->request->query('search', '');
        $order = $this->request->query('order', 'DESC');
        $page = max((int) $this->request->query('page', '1'), 1);
        $limit = 50;
        $offset = ($page - 1) * $limit;

        $filters = ['limit' => $limit, 'offset' => $offset, 'order' => $order];
        if (!empty($level)) $filters['level'] = $level;
        if (!empty($search)) $filters['search'] = $search;

        $logs = Logger::getAll($filters);
        $total = Logger::count($filters);
        $totalPages = max((int) ceil($total / $limit), 1);
        $levelCounts = Logger::getLevels();

        return View::render('system/logs', [
            'title' => 'مدیریت لاگ‌ها',
            'logs' => $logs,
            'level' => $level,
            'search' => $search,
            'order' => $order,
            'page' => $page,
            'totalPages' => $totalPages,
            'total' => $total,
            'levelCounts' => $levelCounts,
        ], true, 'admin');
    }

    public function clear(): string
    {
        $this->requireAdmin();
        Logger::clear();
        View::redirect('/system/logs');
        return '';
    }

    public function detail(string $id): string
    {
        $this->requireAdmin();

        $log = App::db()->fetch('SELECT * FROM system_logs WHERE id = ?', [$id]);
        if (!$log) {
            View::redirect('/system/logs');
            return '';
        }

        return View::render('system/log-detail', [
            'title' => 'جزئیات لاگ',
            'log' => $log,
        ], true, 'admin');
    }
}
