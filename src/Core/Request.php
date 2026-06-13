<?php

namespace App\Core;

class Request
{
    private array $shopContext = [];

    public function method(): string
    {
        return strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');
    }

    public function uri(): string
    {
        $uri = $_SERVER['REQUEST_URI'] ?? '/';
        return strtok($uri, '?');
    }

    public function host(): string
    {
        return $_SERVER['HTTP_HOST'] ?? '';
    }

    public function scheme(): string
    {
        return (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    }

    public function fullUrl(): string
    {
        return $this->scheme() . '://' . $this->host() . $_SERVER['REQUEST_URI'];
    }

    public function query(string $key, mixed $default = null): mixed
    {
        return $_GET[$key] ?? $default;
    }

    public function input(string $key, mixed $default = null): mixed
    {
        return $_POST[$key] ?? $default;
    }

    public function all(): array
    {
        return $_POST;
    }

    public function file(string $key): ?array
    {
        return isset($_FILES[$key]) && $_FILES[$key]['error'] === UPLOAD_ERR_OK
            ? $_FILES[$key] : null;
    }

    public function cookie(string $key, mixed $default = null): mixed
    {
        return $_COOKIE[$key] ?? $default;
    }

    public function session(string $key, mixed $default = null): mixed
    {
        return $_SESSION[$key] ?? $default;
    }

    public function setSession(string $key, mixed $value): void
    {
        $_SESSION[$key] = $value;
    }

    public function removeSession(string $key): void
    {
        unset($_SESSION[$key]);
    }

    public function hasSession(string $key): bool
    {
        return isset($_SESSION[$key]);
    }

    public function setCookie(string $name, string $value, int $expiry = 86400 * 30): void
    {
        setcookie($name, $value, time() + $expiry, '/', '', false, true);
    }

    public function removeCookie(string $name): void
    {
        setcookie($name, '', time() - 3600, '/');
    }

    public function isShopContext(): bool
    {
        return !empty($this->shopContext);
    }

    public function getShopContext(): ?array
    {
        return $this->shopContext ?: null;
    }

    public function setShopContext(array $shop): void
    {
        $this->shopContext = $shop;
    }

    public function isPost(): bool
    {
        return $this->method() === 'POST';
    }

    public function isSecure(): bool
    {
        return $this->scheme() === 'https';
    }
}
