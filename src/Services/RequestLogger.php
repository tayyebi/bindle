<?php

namespace App\Services;

use App\Core\App;

class RequestLogger
{
    public static function logRequest(
        string $method,
        string $url,
        int $statusCode,
        int $durationMs,
        string $host = '',
        ?array $shopContext = null,
        string $userType = '',
        string $userId = '',
        string $ip = '',
        string $userAgent = ''
    ): void {
        try {
            if ($ip === '') {
                $ip = $_SERVER['REMOTE_ADDR'] ?? '';
            }
            if ($userAgent === '') {
                $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';
            }

            App::db()->insert('request_logs', [
                'method' => $method,
                'url' => $url,
                'host' => $host,
                'status_code' => $statusCode,
                'duration_ms' => $durationMs,
                'ip' => $ip,
                'user_agent' => $userAgent,
                'shop_id' => $shopContext['id'] ?? null,
                'shop_domain' => $shopContext['domain'] ?? '',
                'user_type' => $userType,
                'user_id' => $userId,
            ]);
        } catch (\Throwable $e) {
            error_log("RequestLogger failed: " . $e->getMessage());
        }
    }

    public static function logCrawl(
        string $url,
        ?string $shopId,
        int $httpCode,
        int $durationMs,
        bool $success,
        string $parserUsed = '',
        string $errorMessage = '',
        ?array $productData = null
    ): void {
        try {
            App::db()->insert('crawl_logs', [
                'url' => $url,
                'shop_id' => $shopId,
                'http_code' => $httpCode,
                'duration_ms' => $durationMs,
                'success' => $success,
                'parser_used' => $parserUsed,
                'error_message' => $errorMessage,
                'product_name' => $productData['name'] ?? '',
                'product_price' => $productData['price'] ?? null,
            ]);
        } catch (\Throwable $e) {
            error_log("RequestLogger::logCrawl failed: " . $e->getMessage());
        }
    }

    public static function getRequests(array $filters = []): array
    {
        $sql = 'SELECT * FROM request_logs WHERE 1=1';
        $params = [];

        if (!empty($filters['method'])) {
            $sql .= ' AND method = :method';
            $params['method'] = $filters['method'];
        }
        if (!empty($filters['host'])) {
            $sql .= ' AND host = :host';
            $params['host'] = $filters['host'];
        }
        if (!empty($filters['status_min'])) {
            $sql .= ' AND status_code >= :status_min';
            $params['status_min'] = (int) $filters['status_min'];
        }
        if (!empty($filters['status_max'])) {
            $sql .= ' AND status_code <= :status_max';
            $params['status_max'] = (int) $filters['status_max'];
        }
        if (!empty($filters['search'])) {
            $sql .= ' AND (url ILIKE :search OR host ILIKE :search2)';
            $params['search'] = '%' . $filters['search'] . '%';
            $params['search2'] = '%' . $filters['search'] . '%';
        }
        if (!empty($filters['shop_id'])) {
            $sql .= ' AND shop_id = :shop_id';
            $params['shop_id'] = $filters['shop_id'];
        }

        $order = ($filters['order'] ?? 'DESC') === 'ASC' ? 'ASC' : 'DESC';
        $sql .= " ORDER BY created_at {$order}";

        $limit = min(max((int) ($filters['limit'] ?? 50), 1), 500);
        $offset = max((int) ($filters['offset'] ?? 0), 0);
        $sql .= " LIMIT {$limit} OFFSET {$offset}";

        return App::db()->fetchAll($sql, $params);
    }

    public static function countRequests(array $filters = []): int
    {
        $sql = 'SELECT COUNT(*) as cnt FROM request_logs WHERE 1=1';
        $params = [];

        if (!empty($filters['method'])) {
            $sql .= ' AND method = :method';
            $params['method'] = $filters['method'];
        }
        if (!empty($filters['host'])) {
            $sql .= ' AND host = :host';
            $params['host'] = $filters['host'];
        }
        if (!empty($filters['status_min'])) {
            $sql .= ' AND status_code >= :status_min';
            $params['status_min'] = (int) $filters['status_min'];
        }
        if (!empty($filters['status_max'])) {
            $sql .= ' AND status_code <= :status_max';
            $params['status_max'] = (int) $filters['status_max'];
        }
        if (!empty($filters['search'])) {
            $sql .= ' AND (url ILIKE :search OR host ILIKE :search2)';
            $params['search'] = '%' . $filters['search'] . '%';
            $params['search2'] = '%' . $filters['search'] . '%';
        }

        $row = App::db()->fetch($sql, $params);
        return (int) ($row['cnt'] ?? 0);
    }

    public static function getCrawls(array $filters = []): array
    {
        $sql = 'SELECT * FROM crawl_logs WHERE 1=1';
        $params = [];

        if (isset($filters['success']) && $filters['success'] !== '') {
            $sql .= ' AND success = :success';
            $params['success'] = $filters['success'] ? 't' : 'f';
        }
        if (!empty($filters['http_code'])) {
            $sql .= ' AND http_code = :http_code';
            $params['http_code'] = (int) $filters['http_code'];
        }
        if (!empty($filters['search'])) {
            $sql .= ' AND (url ILIKE :search OR product_name ILIKE :search2 OR error_message ILIKE :search3)';
            $params['search'] = '%' . $filters['search'] . '%';
            $params['search2'] = '%' . $filters['search'] . '%';
            $params['search3'] = '%' . $filters['search'] . '%';
        }
        if (!empty($filters['shop_id'])) {
            $sql .= ' AND shop_id = :shop_id';
            $params['shop_id'] = $filters['shop_id'];
        }

        $order = ($filters['order'] ?? 'DESC') === 'ASC' ? 'ASC' : 'DESC';
        $sql .= " ORDER BY created_at {$order}";

        $limit = min(max((int) ($filters['limit'] ?? 50), 1), 500);
        $offset = max((int) ($filters['offset'] ?? 0), 0);
        $sql .= " LIMIT {$limit} OFFSET {$offset}";

        return App::db()->fetchAll($sql, $params);
    }

    public static function countCrawls(array $filters = []): int
    {
        $sql = 'SELECT COUNT(*) as cnt FROM crawl_logs WHERE 1=1';
        $params = [];

        if (isset($filters['success']) && $filters['success'] !== '') {
            $sql .= ' AND success = :success';
            $params['success'] = $filters['success'] ? 't' : 'f';
        }
        if (!empty($filters['http_code'])) {
            $sql .= ' AND http_code = :http_code';
            $params['http_code'] = (int) $filters['http_code'];
        }
        if (!empty($filters['search'])) {
            $sql .= ' AND (url ILIKE :search OR product_name ILIKE :search2)';
            $params['search'] = '%' . $filters['search'] . '%';
            $params['search2'] = '%' . $filters['search'] . '%';
        }

        $row = App::db()->fetch($sql, $params);
        return (int) ($row['cnt'] ?? 0);
    }

    public static function getRequestStats(): array
    {
        $total = App::db()->fetch("SELECT COUNT(*) as cnt FROM request_logs");
        $byMethod = App::db()->fetchAll(
            "SELECT method, COUNT(*) as count FROM request_logs GROUP BY method ORDER BY count DESC"
        );
        $byStatus = App::db()->fetchAll(
            "SELECT (status_code / 100) as group_code, COUNT(*) as count FROM request_logs GROUP BY group_code ORDER BY group_code"
        );
        return [
            'total' => (int) ($total['cnt'] ?? 0),
            'by_method' => $byMethod,
            'by_status' => $byStatus,
        ];
    }

    public static function getCrawlStats(): array
    {
        $total = App::db()->fetch("SELECT COUNT(*) as cnt FROM crawl_logs");
        $success = App::db()->fetch("SELECT COUNT(*) as cnt FROM crawl_logs WHERE success = true");
        $failed = App::db()->fetch("SELECT COUNT(*) as cnt FROM crawl_logs WHERE success = false");
        return [
            'total' => (int) ($total['cnt'] ?? 0),
            'success' => (int) ($success['cnt'] ?? 0),
            'failed' => (int) ($failed['cnt'] ?? 0),
        ];
    }

    public static function clearRequests(): void
    {
        App::db()->query('DELETE FROM request_logs');
    }

    public static function clearCrawls(): void
    {
        App::db()->query('DELETE FROM crawl_logs');
    }

    public static function findRequest(string $id): ?array
    {
        $row = App::db()->fetch('SELECT * FROM request_logs WHERE id = ?', [$id]);
        return $row ?: null;
    }

    public static function findCrawl(string $id): ?array
    {
        $row = App::db()->fetch('SELECT * FROM crawl_logs WHERE id = ?', [$id]);
        return $row ?: null;
    }
}
