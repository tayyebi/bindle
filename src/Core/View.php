<?php

namespace App\Core;

class View
{
    private static string $viewsPath = __DIR__ . '/../Views';

    public static function render(string $view, array $data = [], bool $useLayout = true, string $layout = 'shop'): string
    {
        $content = self::renderPartial($view, $data);

        if (!$useLayout) {
            return $content;
        }

        return self::renderPartial("layouts/{$layout}", array_merge($data, ['content' => $content]));
    }

    public static function renderPartial(string $view, array $data = []): string
    {
        $file = self::$viewsPath . '/' . str_replace('.', '/', $view) . '.php';

        if (!file_exists($file)) {
            return "<p>View not found: {$view}</p>";
        }

        extract($data, EXTR_OVERWRITE);
        ob_start();
        include $file;
        return ob_get_clean();
    }

    public static function renderJson(array $data, int $status = 200): string
    {
        http_response_code($status);
        header('Content-Type: application/json');
        return json_encode($data, JSON_UNESCAPED_UNICODE);
    }

    public static function redirect(string $url, int $status = 302): void
    {
        http_response_code($status);
        header("Location: {$url}");
        exit;
    }

    public static function back(): void
    {
        $referer = $_SERVER['HTTP_REFERER'] ?? '/';
        self::redirect($referer);
    }
}
