<?php

declare(strict_types=1);

/**
 * ShopQ Front Controller
 *
 * Every HTTP request enters here. Apache rewrites all routes to this file.
 */

$config = require dirname(__DIR__) . '/bootstrap/app.php';

$app = new Core\App($config);
$app->run();
