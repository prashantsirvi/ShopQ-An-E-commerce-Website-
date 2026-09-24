<?php

declare(strict_types=1);

return [
    'name' => env('APP_NAME', 'ShopQ'),
    'env' => env('APP_ENV', 'local'),
    'debug' => filter_var(env('APP_DEBUG', 'true'), FILTER_VALIDATE_BOOLEAN),
    'url' => rtrim(env('APP_URL', 'http://localhost/shopq/public'), '/'),
    'timezone' => 'Asia/Kolkata',
    'charset' => 'UTF-8',

    'session' => [
        'name' => env('SESSION_NAME', 'shopq_session'),
        'lifetime' => (int) env('SESSION_LIFETIME', 7200),
        'secure' => false,
        'httponly' => true,
        'samesite' => 'Lax',
    ],

    'security' => [
        'csrf_token_name' => env('CSRF_TOKEN_NAME', '_csrf_token'),
    ],

    'upload' => [
        'max_size' => (int) env('MAX_UPLOAD_SIZE', 5242880),
        'allowed_images' => ['image/jpeg', 'image/png', 'image/webp', 'image/gif'],
    ],

    'payment' => [
        'gateway' => env('PAYMENT_GATEWAY', 'dummy'),
        'razorpay_key' => env('RAZORPAY_KEY', ''),
        'razorpay_secret' => env('RAZORPAY_SECRET', ''),
    ],
];
