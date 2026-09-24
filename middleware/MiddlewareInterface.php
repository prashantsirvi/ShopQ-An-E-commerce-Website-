<?php

declare(strict_types=1);

namespace Middleware;

use Core\Request;

/**
 * Contract for HTTP middleware classes.
 */
interface MiddlewareInterface
{
    public function handle(Request $request, callable $next): void;
}
