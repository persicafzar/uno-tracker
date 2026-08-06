<?php

namespace Core;

class Request
{
    private array $get;
    private array $post;
    private array $server;
    private array $files;
    private array $cookies;
    private ?array $json = null;

    public function __construct()
    {
        $this->get     = $_GET;
        $this->post    = $_POST;
        $this->server  = $_SERVER;
        $this->files   = $_FILES;
        $this->cookies = $_COOKIE;
    }

    public function method(): string
    {
        return strtoupper($this->server['REQUEST_METHOD'] ?? 'GET');
    }

    public function uri(): string
    {
        $uri = $this->server['REQUEST_URI'] ?? '/';
        $uri = parse_url($uri, PHP_URL_PATH);
        return rtrim($uri, '/') ?: '/';
    }

    public function isHtmx(): bool
    {
        return ($this->server['HTTP_HX_REQUEST'] ?? '') === 'true';
    }

    public function isAjax(): bool
    {
        return ($this->server['HTTP_X_REQUESTED_WITH'] ?? '') === 'XMLHttpRequest';
    }

    public function isPost(): bool
    {
        return $this->method() === 'POST';
    }

    public function isGet(): bool
    {
        return $this->method() === 'GET';
    }

    public function get(string $key, $default = null)
    {
        return $this->get[$key] ?? $default;
    }

    public function post(string $key, $default = null)
    {
        return $this->post[$key] ?? $default;
    }

    public function input(string $key, $default = null)
    {
        return $this->post[$key] ?? $this->get[$key] ?? $default;
    }

    public function all(): array
    {
        return array_merge($this->get, $this->post);
    }

    public function only(array $keys): array
    {
        $result = [];
        foreach ($keys as $key) {
            if (isset($this->post[$key])) {
                $result[$key] = $this->post[$key];
            } elseif (isset($this->get[$key])) {
                $result[$key] = $this->get[$key];
            }
        }
        return $result;
    }

    public function json(): ?array
    {
        if ($this->json === null) {
            $raw = file_get_contents('php://input');
            $this->json = json_decode($raw, true);
        }
        return $this->json;
    }

    public function file(string $key): ?array
    {
        return $this->files[$key] ?? null;
    }

    public function hasFile(string $key): bool
    {
        return isset($this->files[$key]) && $this->files[$key]['error'] !== UPLOAD_ERR_NO_FILE;
    }

    public function cookie(string $key, $default = null)
    {
        return $this->cookies[$key] ?? $default;
    }

    public function header(string $key, $default = null): ?string
    {
        $key = 'HTTP_' . strtoupper(str_replace('-', '_', $key));
        return $this->server[$key] ?? $default;
    }

    public function ip(): string
    {
        return $this->server['HTTP_X_FORWARDED_FOR']
            ?? $this->server['HTTP_CLIENT_IP']
            ?? $this->server['REMOTE_ADDR']
            ?? '0.0.0.0';
    }

    public function userAgent(): string
    {
        return $this->server['HTTP_USER_AGENT'] ?? '';
    }

    public function session(string $key, $default = null)
    {
        return $_SESSION[$key] ?? $default;
    }

    public function setSession(string $key, $value): void
    {
        $_SESSION[$key] = $value;
    }

    public function removeSession(string $key): void
    {
        unset($_SESSION[$key]);
    }

    public function flash(string $key, $value): void
    {
        $_SESSION['_flash'][$key] = $value;
    }

    public function getFlash(string $key, $default = null)
    {
        $value = $_SESSION['_flash'][$key] ?? $default;
        unset($_SESSION['_flash'][$key]);
        return $value;
    }
    /**
     * گرفتن مقدار از $_SERVER
     */
    public function server(string $key, $default = null)
    {
        return $_SERVER[$key] ?? $default;
    }
}
