<?php

declare(strict_types=1);

chdir(dirname(__DIR__) . '/public');

$_SERVER['REQUEST_METHOD'] = 'GET';
$_SERVER['REQUEST_URI'] = '/shopq/public/';
$_SERVER['SCRIPT_NAME'] = '/shopq/public/index.php';

ob_start();
require 'index.php';
$output = ob_get_clean();

$checks = [
    'Featured Products' => str_contains($output, 'Featured Products'),
    'Shop by Category' => str_contains($output, 'Shop by Category'),
    'product-card' => str_contains($output, 'product-card'),
    'footer-grid' => str_contains($output, 'footer-grid'),
];

foreach ($checks as $label => $ok) {
    echo ($ok ? '[OK]' : '[FAIL]') . ' ' . $label . PHP_EOL;
}
