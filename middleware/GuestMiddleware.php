<?php

declare(strict_types=1);

namespace Middleware;

use Core\Request;
use Core\Response;

/**
 * Redirect authenticated customers away from guest-only pages.
 */
final class GuestMiddleware implements MiddlewareInterface
{
    public function handle(Request $request, callable $next): void
    {
        if (is_customer_authenticated()) {
            Response::redirect(url('/profile'));
        }

        $next();
    }
}
