<?php

namespace Core;

class Response
{
    private int $statusCode = 200;
    private array $headers = [];
    private string $body = '';

    public function status(int $code): self
    {
        $this->statusCode = $code;
        return $this;
    }

    public function header(string $name, string $value): self
    {
        $this->headers[$name] = $value;
        return $this;
    }

    public function body(string $content): self
    {
        $this->body = $content;
        return $this;
    }

    public function json(array $data, int $statusCode = 200): void
    {
        $this->statusCode = $statusCode;
        $this->headers['Content-Type'] = 'application/json; charset=utf-8';
        $this->body = json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        $this->send();
    }

    public function html(string $html, int $statusCode = 200): void
    {
        $this->statusCode = $statusCode;
        $this->headers['Content-Type'] = 'text/html; charset=utf-8';
        $this->body = $html;
        $this->send();
    }

    public function redirect(string $url, int $statusCode = 302): void
    {
        $this->statusCode = $statusCode;
        $this->headers['Location'] = $url;
        $this->send();
    }

    public function htmxRedirect(string $url): void
    {
        $this->headers['HX-Redirect'] = $url;
        $this->send();
    }

    public function htmxRefresh(): void
    {
        $this->headers['HX-Refresh'] = 'true';
        $this->send();
    }

    public function notModified(): void
    {
        $this->statusCode = 304;
        $this->send();
    }

    public function withEtag(string $content): self
    {
        $etag = '"' . md5($content) . '"';
        $this->headers['ETag'] = $etag;

        if (isset($_SERVER['HTTP_IF_NONE_MATCH']) && $_SERVER['HTTP_IF_NONE_MATCH'] === $etag) {
            $this->notModified();
            exit;
        }

        return $this;
    }

    public function noCache(): self
    {
        $this->headers['Cache-Control'] = 'no-cache, no-store, must-revalidate';
        $this->headers['Pragma'] = 'no-cache';
        $this->headers['Expires'] = '0';
        return $this;
    }

    public function send(): void
    {
        http_response_code($this->statusCode);

        foreach ($this->headers as $name => $value) {
            header("{$name}: {$value}");
        }

        echo $this->body;
        exit;
    }
}