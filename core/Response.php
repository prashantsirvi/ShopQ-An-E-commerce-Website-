<?php

declare(strict_types=1);

namespace Core;

/**
 * HTTP response helpers for redirects and JSON APIs.
 */
final class Response
{
    public static function redirect(string $url, int $status = 302): never
    {
        header('Location: ' . $url, true, $status);
        exit;
    }

    public static function json(array $data, int $status = 200): never
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE);
        exit;
    }

    public static function back(): never
    {
        $referer = $_SERVER['HTTP_REFERER'] ?? url('/');
        self::redirect($referer);
    }
}
