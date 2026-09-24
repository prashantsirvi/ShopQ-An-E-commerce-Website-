<?php

declare(strict_types=1);

namespace Core;

/**
 * Thin HTTP request wrapper around superglobals.
 */
final class Request
{
    public function __construct(
        private readonly array $query,
        private readonly array $request,
        private readonly array $server,
        private readonly array $files,
        private readonly array $cookies
    ) {
    }

    public static function capture(): self
    {
        return new self($_GET, $_POST, $_SERVER, $_FILES, $_COOKIE);
    }

    public function method(): string
    {
        $method = strtoupper($this->server['REQUEST_METHOD'] ?? 'GET');

        if ($method === 'POST') {
            $override = $this->input('_method');

            if (in_array(strtoupper((string) $override), ['PUT', 'PATCH', 'DELETE'], true)) {
                return strtoupper((string) $override);
            }
        }

        return $method;
    }

    public function uri(): string
    {
        $uri = $this->server['REQUEST_URI'] ?? '/';
        $path = strtok($uri, '?') ?: '/';

        return strip_base_path($path);
    }

    public function input(string $key, mixed $default = null): mixed
    {
        return $this->request[$key] ?? $this->query[$key] ?? $default;
    }

    public function all(): array
    {
        return array_merge($this->query, $this->request);
    }

    public function query(string $key, mixed $default = null): mixed
    {
        return $this->query[$key] ?? $default;
    }

    public function has(string $key): bool
    {
        return array_key_exists($key, $this->request) || array_key_exists($key, $this->query);
    }

    public function isAjax(): bool
    {
        return strtolower($this->server['HTTP_X_REQUESTED_WITH'] ?? '') === 'xmlhttprequest';
    }

    public function ip(): string
    {
        return $this->server['REMOTE_ADDR'] ?? '0.0.0.0';
    }

    public function files(string $key): ?array
    {
        return $this->files[$key] ?? null;
    }

    public function cookie(string $key, mixed $default = null): mixed
    {
        return $this->cookies[$key] ?? $default;
    }
}
