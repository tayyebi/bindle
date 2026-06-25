<?php

namespace App\Controllers;

use App\Core\App;
use App\Core\Request;
use App\Core\View;
use App\Services\Logger;
use App\Services\RequestLogger;

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

        $tab = $this->request->query('tab', 'errors');

        return match ($tab) {
            'crawls' => $this->renderCrawls(),
            'requests' => $this->renderRequests(),
            default => $this->renderErrors(),
        };
    }

    private function renderErrors(): string
    {
        $level = $this->request->query('level', '');
        $search = $this->request->query('search', '');
        $order = $this->request->query('order', 'DESC');
        $page = max((int) $this->request->query('page', '1'), 1);
        $perPage = min(max((int) $this->request->query('per_page', '50'), 10), 200);
        $offset = ($page - 1) * $perPage;

        $filters = ['limit' => $perPage, 'offset' => $offset, 'order' => $order];
        if (!empty($level)) $filters['level'] = $level;
        if (!empty($search)) $filters['search'] = $search;

        $logs = Logger::getAll($filters);
        $total = Logger::count($filters);
        $totalPages = max((int) ceil($total / $perPage), 1);
        $levelCounts = Logger::getLevels();

        return View::render('system/logs', [
            'title' => 'مدیریت لاگ‌ها',
            'activeTab' => 'errors',
            'logs' => $logs,
            'level' => $level,
            'search' => $search,
            'order' => $order,
            'page' => $page,
            'perPage' => $perPage,
            'totalPages' => $totalPages,
            'total' => $total,
            'levelCounts' => $levelCounts,
            'tabStats' => [
                'errors' => Logger::count(),
                'crawls' => RequestLogger::countCrawls(),
                'requests' => RequestLogger::countRequests(),
            ],
            'crawlStats' => null,
            'requestStats' => null,
        ], true, 'admin');
    }

    private function renderCrawls(): string
    {
        $search = $this->request->query('search', '');
        $order = $this->request->query('order', 'DESC');
        $success = $this->request->query('success', '');
        $httpCode = $this->request->query('http_code', '');
        $page = max((int) $this->request->query('page', '1'), 1);
        $perPage = min(max((int) $this->request->query('per_page', '50'), 10), 200);
        $offset = ($page - 1) * $perPage;

        $filters = ['limit' => $perPage, 'offset' => $offset, 'order' => $order];
        if ($success !== '') $filters['success'] = $success === '1';
        if (!empty($httpCode)) $filters['http_code'] = (int) $httpCode;
        if (!empty($search)) $filters['search'] = $search;

        $logs = RequestLogger::getCrawls($filters);
        $total = RequestLogger::countCrawls($filters);
        $totalPages = max((int) ceil($total / $perPage), 1);
        $crawlStats = RequestLogger::getCrawlStats();

        return View::render('system/logs', [
            'title' => 'مدیریت لاگ‌ها',
            'activeTab' => 'crawls',
            'logs' => $logs,
            'search' => $search,
            'order' => $order,
            'success' => $success,
            'httpCode' => $httpCode,
            'page' => $page,
            'perPage' => $perPage,
            'totalPages' => $totalPages,
            'total' => $total,
            'levelCounts' => [],
            'tabStats' => [
                'errors' => Logger::count(),
                'crawls' => RequestLogger::countCrawls(),
                'requests' => RequestLogger::countRequests(),
            ],
            'crawlStats' => $crawlStats,
            'requestStats' => null,
        ], true, 'admin');
    }

    private function renderRequests(): string
    {
        $search = $this->request->query('search', '');
        $order = $this->request->query('order', 'DESC');
        $method = $this->request->query('method', '');
        $host = $this->request->query('host', '');
        $statusCode = $this->request->query('status_code', '');
        $page = max((int) $this->request->query('page', '1'), 1);
        $perPage = min(max((int) $this->request->query('per_page', '50'), 10), 200);
        $offset = ($page - 1) * $perPage;

        $filters = ['limit' => $perPage, 'offset' => $offset, 'order' => $order];
        if (!empty($method)) $filters['method'] = $method;
        if (!empty($host)) $filters['host'] = $host;
        if (!empty($search)) $filters['search'] = $search;

        if ($statusCode !== '') {
            $statusCode = (int) $statusCode;
            if ($statusCode >= 100 && $statusCode < 600) {
                $filters['status_min'] = $statusCode;
                $filters['status_max'] = $statusCode;
            } elseif ($statusCode > 0) {
                $filters['status_min'] = $statusCode * 100;
                $filters['status_max'] = $statusCode * 100 + 99;
            }
        }

        $logs = RequestLogger::getRequests($filters);
        $total = RequestLogger::countRequests($filters);
        $totalPages = max((int) ceil($total / $perPage), 1);
        $requestStats = RequestLogger::getRequestStats();

        return View::render('system/logs', [
            'title' => 'مدیریت لاگ‌ها',
            'activeTab' => 'requests',
            'logs' => $logs,
            'search' => $search,
            'order' => $order,
            'method' => $method,
            'host' => $host,
            'statusCode' => $statusCode,
            'page' => $page,
            'perPage' => $perPage,
            'totalPages' => $totalPages,
            'total' => $total,
            'levelCounts' => [],
            'tabStats' => [
                'errors' => Logger::count(),
                'crawls' => RequestLogger::countCrawls(),
                'requests' => RequestLogger::countRequests(),
            ],
            'crawlStats' => null,
            'requestStats' => $requestStats,
        ], true, 'admin');
    }

    public function detail(string $id): string
    {
        $this->requireAdmin();

        $tab = $this->request->query('tab', 'errors');

        $log = match ($tab) {
            'crawls' => RequestLogger::findCrawl($id),
            'requests' => RequestLogger::findRequest($id),
            default => App::db()->fetch('SELECT * FROM system_logs WHERE id = ?', [$id]),
        };

        if (!$log) {
            View::redirect('/system/logs?tab=' . urlencode($tab));
            return '';
        }

        return View::render('system/log-detail', [
            'title' => 'جزئیات لاگ',
            'log' => $log,
            'tab' => $tab,
        ], true, 'admin');
    }

    public function clear(): string
    {
        $this->requireAdmin();

        $tab = $this->request->query('tab', 'errors');

        match ($tab) {
            'crawls' => RequestLogger::clearCrawls(),
            'requests' => RequestLogger::clearRequests(),
            default => Logger::clear(),
        };

        View::redirect('/system/logs?tab=' . urlencode($tab));
        return '';
    }

    public function export(): void
    {
        $this->requireAdmin();

        $tab = $this->request->query('tab', 'errors');
        $format = $this->request->query('format', 'csv');

        $rows = match ($tab) {
            'crawls' => RequestLogger::getCrawls(['limit' => 5000, 'order' => 'DESC']),
            'requests' => RequestLogger::getRequests(['limit' => 5000, 'order' => 'DESC']),
            default => Logger::getAll(['limit' => 5000, 'order' => 'DESC']),
        };

        $filename = match ($tab) {
            'crawls' => 'crawl-logs',
            'requests' => 'request-logs',
            default => 'error-logs',
        };

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '-' . date('Ymd') . '.csv"');

        $output = fopen('php://output', 'w');
        fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF));

        if (!empty($rows)) {
            fputcsv($output, array_keys($rows[0]));
            foreach ($rows as $row) {
                fputcsv($output, $row);
            }
        }

        fclose($output);
        exit;
    }
}
