<?php

declare(strict_types=1);

chdir(dirname(__DIR__) . '/public');

$_SERVER['REQUEST_METHOD'] = 'GET';
$_SERVER['SCRIPT_NAME'] = '/shopq/public/index.php';
$_SERVER['REQUEST_URI'] = '/shopq/public/orders';

ob_start();

try {
    require 'index.php';
} catch (Throwable $exception) {
    echo 'ERROR: ' . $exception->getMessage();
}

$output = ob_get_clean();
$headers = headers_list();
$redirected = false;

foreach ($headers as $header) {
    if (stripos($header, 'Location:') !== false && stripos($header, 'login') !== false) {
        $redirected = true;
        break;
    }
}

if ($redirected || str_contains($output, '/login') || str_contains($output, 'Login')) {
    echo "PASS orders redirects to login for guests\n";
    exit(0);
}

echo "FAIL orders route\n";
exit(1);
