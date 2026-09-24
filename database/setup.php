<?php

declare(strict_types=1);

/**
 * ShopQ Database Installer
 *
 * Usage:
 *   C:\xampp\php\php.exe database/setup.php
 *   C:\xampp\php\php.exe database/setup.php --fresh   (drops and recreates database)
 */

require_once dirname(__DIR__) . '/bootstrap/app.php';
require_once __DIR__ . '/seeds/ProductSeeder.php';

$config = require CONFIG_PATH . '/database.php';
$fresh = in_array('--fresh', $argv ?? [], true);

echo "ShopQ Database Setup\n";
echo str_repeat('=', 40) . "\n";

try {
    $dsn = sprintf(
        '%s:host=%s;port=%d;charset=%s',
        $config['driver'],
        $config['host'],
        $config['port'],
        $config['charset']
    );

    $pdo = new PDO($dsn, $config['username'], $config['password'], $config['options']);

    if ($fresh) {
        $pdo->exec('DROP DATABASE IF EXISTS `' . $config['database'] . '`');
        echo "[OK] Existing database dropped (--fresh)\n";
        runSqlFile($pdo, __DIR__ . '/migrations/001_create_schema.sql');
        echo "[OK] Schema migration 001\n";
    } else {
        $databaseExists = (int) $pdo->query(
            "SELECT COUNT(*) FROM information_schema.SCHEMATA WHERE SCHEMA_NAME = " . $pdo->quote($config['database'])
        )->fetchColumn() > 0;

        if (!$databaseExists) {
            runSqlFile($pdo, __DIR__ . '/migrations/001_create_schema.sql');
            echo "[OK] Schema migration 001\n";
        } else {
            echo "[SKIP] Database already exists — running patch 002 only\n";
        }
    }

    runSqlFile($pdo, __DIR__ . '/migrations/002_add_system_tables.sql');
    echo "[OK] Schema migration 002\n";

    $pdo->exec('USE `' . $config['database'] . '`');

    $roleCount = (int) $pdo->query('SELECT COUNT(*) FROM roles')->fetchColumn();

    if ($roleCount === 0) {
        runSqlFile($pdo, __DIR__ . '/seeds/001_core_data.sql');
        echo "[OK] Core seed data imported\n";
    } else {
        echo "[SKIP] Core data already present\n";
    }

    $productCount = (int) $pdo->query('SELECT COUNT(*) FROM products')->fetchColumn();

    if ($productCount === 0) {
        (new ProductSeeder($pdo))->run();
        echo "[OK] 50 products seeded with images and variants\n";
    } else {
        echo "[SKIP] Products already exist ({$productCount})\n";
    }

    $summary = $pdo->query(
        'SELECT
            (SELECT COUNT(*) FROM categories) AS categories,
            (SELECT COUNT(*) FROM products) AS products,
            (SELECT COUNT(*) FROM product_variants) AS variants,
            (SELECT COUNT(*) FROM product_images) AS images,
            (SELECT COUNT(*) FROM users) AS users'
    )->fetch();

    echo str_repeat('-', 40) . "\n";
    echo 'Categories : ' . ($summary['categories'] ?? 0) . "\n";
    echo 'Products   : ' . ($summary['products'] ?? 0) . "\n";
    echo 'Variants   : ' . ($summary['variants'] ?? 0) . "\n";
    echo 'Images     : ' . ($summary['images'] ?? 0) . "\n";
    echo 'Users      : ' . ($summary['users'] ?? 0) . "\n";
    echo str_repeat('=', 40) . "\n";
    echo "Setup complete.\n";
    echo "Admin login: admin@shopq.local / Admin@123\n";
} catch (Throwable $exception) {
    echo '[ERROR] ' . $exception->getMessage() . "\n";
    exit(1);
}

function runSqlFile(PDO $pdo, string $filePath): void
{
    if (!is_file($filePath)) {
        throw new RuntimeException("SQL file not found: {$filePath}");
    }

    $sql = file_get_contents($filePath);

    if ($sql === false) {
        throw new RuntimeException("Unable to read SQL file: {$filePath}");
    }

    $pdo->exec($sql);
}
