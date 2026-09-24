<?php

declare(strict_types=1);

/**
 * PSR-4 inspired class autoloader for ShopQ.
 *
 * Maps namespaces to project directories without Composer.
 */
spl_autoload_register(static function (string $class): void {
    $prefixes = [
        'Core\\'        => ROOT_PATH . '/core/',
        'Controllers\\' => ROOT_PATH . '/controllers/',
        'Models\\'      => ROOT_PATH . '/models/',
        'Middleware\\'  => ROOT_PATH . '/middleware/',
        'Services\\'    => ROOT_PATH . '/services/',
    ];

    foreach ($prefixes as $prefix => $baseDir) {
        $prefixLength = strlen($prefix);

        if (strncmp($prefix, $class, $prefixLength) !== 0) {
            continue;
        }

        $relativeClass = substr($class, $prefixLength);
        $file = $baseDir . str_replace('\\', DIRECTORY_SEPARATOR, $relativeClass) . '.php';

        if (is_file($file)) {
            require_once $file;
        }
    }
});

require_once ROOT_PATH . '/helpers/env.php';
require_once ROOT_PATH . '/helpers/path.php';
require_once ROOT_PATH . '/helpers/session.php';
require_once ROOT_PATH . '/helpers/csrf.php';
require_once ROOT_PATH . '/helpers/sanitize.php';
require_once ROOT_PATH . '/helpers/url.php';
require_once ROOT_PATH . '/helpers/auth.php';
require_once ROOT_PATH . '/helpers/validation.php';
require_once ROOT_PATH . '/helpers/settings.php';
require_once ROOT_PATH . '/helpers/media.php';
require_once ROOT_PATH . '/helpers/pagination.php';
require_once ROOT_PATH . '/helpers/product.php';
require_once ROOT_PATH . '/helpers/commerce.php';
require_once ROOT_PATH . '/helpers/string.php';
require_once ROOT_PATH . '/helpers/activity.php';
