<?php

declare(strict_types=1);

chdir(dirname(__DIR__) . '/public');

$_SERVER['REQUEST_METHOD'] = 'GET';
$_SERVER['SCRIPT_NAME'] = '/shopq/public/index.php';

$tests = [
    '/cart' => 'Shopping Cart',
    '/wishlist' => 'My Wishlist',
    '/compare' => 'Compare Products',
    '/api/commerce/counts' => 'cart_count',
];

$failed = 0;

foreach ($tests as $uri => $needle) {
    $_SERVER['REQUEST_URI'] = '/shopq/public' . $uri;
    ob_start();

    try {
        require 'index.php';
    } catch (Throwable $exception) {
        echo 'ERROR: ' . $exception->getMessage();
    }

    $output = ob_get_clean();
    $ok = str_contains($output, $needle);
    echo ($ok ? 'PASS' : 'FAIL') . " {$uri}\n";

    if (!$ok) {
        $failed++;
    }
}

exit($failed > 0 ? 1 : 0);
