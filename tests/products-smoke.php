<?php

declare(strict_types=1);

chdir(dirname(__DIR__) . '/public');

$tests = [
    '/shopq/public/products' => 'All Products',
    '/shopq/public/category/electronics' => 'Electronics',
    '/shopq/public/search?q=wireless' => 'wireless',
    '/shopq/public/deals' => 'Deals',
];

foreach ($tests as $uri => $needle) {
    $_SERVER['REQUEST_METHOD'] = 'GET';
    $_SERVER['REQUEST_URI'] = $uri;
    $_SERVER['SCRIPT_NAME'] = '/shopq/public/index.php';

    ob_start();
    require 'index.php';
    $output = ob_get_clean();

    echo (str_contains($output, $needle) ? '[OK]' : '[FAIL]') . ' ' . $uri . PHP_EOL;
}

// product detail
$_SERVER['REQUEST_URI'] = '/shopq/public/products/wireless-earbuds-pro';
ob_start();
require 'index.php';
$output = ob_get_clean();
echo (str_contains($output, 'Add to Cart') ? '[OK]' : '[FAIL]') . ' product detail' . PHP_EOL;
