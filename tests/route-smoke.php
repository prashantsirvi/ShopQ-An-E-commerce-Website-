<?php

declare(strict_types=1);

chdir(dirname(__DIR__) . '/public');

$_SERVER['REQUEST_METHOD'] = 'GET';
$_SERVER['SCRIPT_NAME'] = '/shopq/public/index.php';

$tests = ['/', '/products', '/deals', '/unknown'];

foreach ($tests as $testUri) {
    $_SERVER['REQUEST_URI'] = '/shopq/public' . ($testUri === '/' ? '/' : $testUri);

    ob_start();

    try {
        require 'index.php';
    } catch (Throwable $exception) {
        echo 'ERROR: ' . $exception->getMessage();
    }

    $output = ob_get_clean();
    preg_match('/<title>(.*?)<\/title>/', $output, $matches);
    $title = $matches[1] ?? 'no title';

    echo $testUri . ' => ' . $title . PHP_EOL;
}
