<?php

declare(strict_types=1);

chdir(dirname(__DIR__) . '/public');

$_SERVER['REQUEST_METHOD'] = 'GET';
$_SERVER['REQUEST_URI'] = '/shopq/public/health';
$_SERVER['SCRIPT_NAME'] = '/shopq/public/index.php';

ob_start();
require 'index.php';
$output = ob_get_clean();

echo $output . PHP_EOL;
