<?php

declare(strict_types=1);

/**
 * Application bootstrap — loads environment, helpers, and autoloader.
 */

define('ROOT_PATH', dirname(__DIR__));
define('APP_PATH', ROOT_PATH);
define('CONFIG_PATH', ROOT_PATH . '/config');
define('CORE_PATH', ROOT_PATH . '/core');
define('CONTROLLERS_PATH', ROOT_PATH . '/controllers');
define('MODELS_PATH', ROOT_PATH . '/models');
define('VIEWS_PATH', ROOT_PATH . '/views');
define('HELPERS_PATH', ROOT_PATH . '/helpers');
define('STORAGE_PATH', ROOT_PATH . '/storage');
define('LOGS_PATH', ROOT_PATH . '/logs');
define('UPLOADS_PATH', ROOT_PATH . '/uploads');
define('PUBLIC_PATH', ROOT_PATH . '/public');

require_once ROOT_PATH . '/bootstrap/autoload.php';

loadEnv(ROOT_PATH . '/.env');

$config = require CONFIG_PATH . '/app.php';

date_default_timezone_set($config['timezone'] ?? 'Asia/Kolkata');

if ($config['debug'] ?? false) {
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
} else {
    error_reporting(E_ALL & ~E_DEPRECATED & ~E_STRICT);
    ini_set('display_errors', '0');
}

ini_set('log_errors', '1');
ini_set('error_log', LOGS_PATH . '/php-errors.log');

configureSession($config['session'] ?? []);

return $config;
