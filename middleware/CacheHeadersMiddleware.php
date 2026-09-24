<?php

declare(strict_types=1);

namespace Middleware;

use Core\Request;

/** Adds cache-friendly headers for safe GET responses. */
final class CacheHeadersMiddleware implements MiddlewareInterface
{
    public function handle(Request $request, callable $next): void
    {
        if ($request->method() === 'GET' && !str_starts_with($request->uri(), '/admin')) {
            header('Cache-Control: private, max-age=0, must-revalidate');
        }

        $next();
    }
}
