<?php

declare(strict_types=1);

$tests = [
    'health-smoke.php',
    'route-smoke.php',
    'home-smoke.php',
    'products-smoke.php',
    'commerce-smoke.php',
    'orders-smoke.php',
];

$failed = 0;

foreach ($tests as $test) {
    $path = __DIR__ . '/' . $test;

    if (!is_file($path)) {
        echo "[SKIP] {$test}\n";
        continue;
    }

    echo "=== {$test} ===\n";
    passthru(PHP_BINARY . ' ' . escapeshellarg($path), $exitCode);

    if ($exitCode !== 0) {
        $failed++;
    }

    echo "\n";
}

exit($failed > 0 ? 1 : 0);
