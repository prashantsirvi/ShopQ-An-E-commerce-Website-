<?php

declare(strict_types=1);

namespace Middleware;

use Core\Request;
use Core\Response;

/**
 * Restricts routes to admin users with valid admin session.
 * Full RBAC implementation arrives in Phase 4 and Phase 13.
 */
final class AdminMiddleware implements MiddlewareInterface
{
    public function handle(Request $request, callable $next): void
    {
        if (!is_admin_authenticated()) {
            if ($request->isAjax()) {
                Response::json([
                    'success' => false,
                    'message' => 'Admin authentication required.',
                ], 401);
            }

            Response::redirect(url('/admin/login'));
        }

        $next();
    }
}
