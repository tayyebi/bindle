<?php

namespace App\Services;

use App\Core\App;

class Logger
{
    private static bool $registered = false;

    public static function register(): void
    {
        if (self::$registered) return;
        self::$registered = true;

        set_error_handler([self::class, 'handleError']);
        set_exception_handler([self::class, 'handleException']);
        register_shutdown_function([self::class, 'handleShutdown']);
    }

    public static function handleError(
        int $severity,
        string $message,
        string $file = '',
        int $line = 0
    ): bool {
        if (!(error_reporting() & $severity)) {
            return false;
        }

        $level = match ($severity) {
            E_WARNING, E_USER_WARNING => 'warning',
            E_NOTICE, E_USER_NOTICE, E_DEPRECATED, E_USER_DEPRECATED => 'notice',
            E_STRICT => 'notice',
            default => 'error',
        };

        self::log($level, $message, $file, $line, debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS));

        return false;
    }

    public static function handleException(\Throwable $e): void
    {
        self::log('critical', $e->getMessage(), $e->getFile(), $e->getLine(), $e->getTraceAsString());

        if (php_sapi_name() !== 'cli') {
            http_response_code(500);
            echo \App\Core\View::render('errors/500', [
                'message' => (App::config('app.env') === 'development')
                    ? $e->getMessage() : 'خطای داخلی سرور',
            ], true, 'admin');
        } else {
            echo "[CRITICAL] " . $e->getMessage() . "\n";
            echo "  in " . $e->getFile() . ":" . $e->getLine() . "\n";
        }
    }

    public static function handleShutdown(): void
    {
        $error = error_get_last();
        if ($error && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) {
            self::log('critical', $error['message'], $error['file'], $error['line']);
        }
    }

    public static function log(
        string $level,
        string $message,
        string $file = '',
        int $line = 0,
        string|array $trace = ''
    ): void {
        if (is_array($trace)) {
            $traceStr = '';
            foreach (array_slice($trace, 0, 10) as $i => $frame) {
                $f = $frame['file'] ?? '[internal]';
                $l = $frame['line'] ?? 0;
                $fn = $frame['function'] ?? '';
                $cls = $frame['class'] ?? '';
                $traceStr .= "#{$i} {$f}({$l}): {$cls}{$fn}\n";
            }
            $trace = $traceStr;
        }

        try {
            $requestMethod = $_SERVER['REQUEST_METHOD'] ?? '';
            $requestUrl = $_SERVER['REQUEST_URI'] ?? '';
            $requestIp = $_SERVER['REMOTE_ADDR'] ?? '';

            $userId = '';
            $userType = '';
            if (isset($_SESSION['admin_id'])) {
                $userType = 'admin';
                $userId = $_SESSION['admin_id'];
            } elseif (isset($_SESSION['shop_id'])) {
                $userType = 'shop';
                $userId = $_SESSION['shop_id'];
            }

            App::db()->insert('system_logs', [
                'level' => $level,
                'message' => $message,
                'file' => $file,
                'line' => $line,
                'trace' => $trace,
                'user_type' => $userType,
                'user_id' => $userId,
                'request_method' => $requestMethod,
                'request_url' => $requestUrl,
                'request_ip' => $requestIp,
            ]);

            self::dispatchWebhooks($level, $message, $file, $line);
        } catch (\Throwable $e) {
            error_log("Logger failed: " . $e->getMessage());
        }
    }

    public static function getAll(array $filters = []): array
    {
        $sql = 'SELECT * FROM system_logs WHERE 1=1';
        $params = [];

        if (!empty($filters['level'])) {
            $sql .= ' AND level = :level';
            $params['level'] = $filters['level'];
        }

        if (!empty($filters['search'])) {
            $sql .= ' AND message ILIKE :search';
            $params['search'] = '%' . $filters['search'] . '%';
        }

        $order = ($filters['order'] ?? 'DESC') === 'ASC' ? 'ASC' : 'DESC';
        $sql .= " ORDER BY created_at {$order}";

        $limit = min(max((int) ($filters['limit'] ?? 100), 1), 500);
        $offset = max((int) ($filters['offset'] ?? 0), 0);
        $sql .= " LIMIT {$limit} OFFSET {$offset}";

        return App::db()->fetchAll($sql, $params);
    }

    public static function count(array $filters = []): int
    {
        $sql = 'SELECT COUNT(*) as cnt FROM system_logs WHERE 1=1';
        $params = [];

        if (!empty($filters['level'])) {
            $sql .= ' AND level = :level';
            $params['level'] = $filters['level'];
        }

        if (!empty($filters['search'])) {
            $sql .= ' AND message ILIKE :search';
            $params['search'] = '%' . $filters['search'] . '%';
        }

        $row = App::db()->fetch($sql, $params);
        return (int) ($row['cnt'] ?? 0);
    }

    public static function getLevels(): array
    {
        return App::db()->fetchAll(
            "SELECT level, COUNT(*) as count FROM system_logs GROUP BY level ORDER BY count DESC"
        );
    }

    public static function clear(): void
    {
        App::db()->query('DELETE FROM system_logs');
    }

    private static function dispatchWebhooks(string $level, string $message, string $file, int $line): void
    {
        if (!in_array($level, ['error', 'critical'])) return;

        try {
            EventService::dispatch('error.' . $level, [
                'level' => $level,
                'message' => $message,
                'file' => $file,
                'line' => $line,
                'time' => date('c'),
            ]);
        } catch (\Throwable $e) {
            error_log("Webhook dispatch failed: " . $e->getMessage());
        }
    }
}
