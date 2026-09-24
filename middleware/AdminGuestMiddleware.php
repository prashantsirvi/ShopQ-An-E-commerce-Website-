<?php

declare(strict_types=1);

namespace Middleware;

use Core\Request;
use Core\Response;

/**
 * Redirect authenticated admins away from admin login page.
 */
final class AdminGuestMiddleware implements MiddlewareInterface
{
    public function handle(Request $request, callable $next): void
    {
        if (is_admin_authenticated()) {
            Response::redirect(url('/admin/dashboard'));
        }

        $next();
    }
}
