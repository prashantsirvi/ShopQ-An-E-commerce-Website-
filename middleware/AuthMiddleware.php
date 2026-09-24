<?php

declare(strict_types=1);

namespace Middleware;

use Core\Request;
use Core\Response;

/**
 * Ensures a customer is authenticated before accessing protected routes.
 * Full implementation arrives in Phase 4 (Authentication).
 */
final class AuthMiddleware implements MiddlewareInterface
{
    public function handle(Request $request, callable $next): void
    {
        if (!is_customer_authenticated()) {
            if ($request->isAjax()) {
                Response::json([
                    'success' => false,
                    'message' => 'Authentication required.',
                ], 401);
            }

            $_SESSION['_intended_url'] = $request->uri();
            Response::redirect(url('/login'));
        }

        $next();
    }
}
