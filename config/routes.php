<?php

declare(strict_types=1);

use Controllers\AdminAuthController;
use Controllers\AdminBannerController;
use Controllers\AdminCategoryController;
use Controllers\AdminCouponController;
use Controllers\AdminCustomerController;
use Controllers\AdminDashboardController;
use Controllers\AdminOrderController;
use Controllers\AdminProductController;
use Controllers\AdminReviewController;
use Controllers\AdminSettingsController;
use Controllers\AuthController;
use Controllers\CartController;
use Controllers\CheckoutController;
use Controllers\CompareController;
use Controllers\HomeController;
use Controllers\OrderController;
use Controllers\PageController;
use Controllers\PaymentController;
use Controllers\ProductController;
use Controllers\ProfileController;
use Controllers\WishlistController;
use Core\App;
use Middleware\AdminGuestMiddleware;
use Middleware\AdminMiddleware;
use Middleware\AuthMiddleware;
use Middleware\GuestMiddleware;

/** @var App $app */
$router = $app->router();

$router->get('/', [HomeController::class, 'index']);
$router->get('/products', [ProductController::class, 'index']);
$router->get('/products/{slug}', [ProductController::class, 'show']);
$router->get('/category/{slug}', [ProductController::class, 'category']);
$router->get('/search', [ProductController::class, 'search']);
$router->get('/api/search/suggestions', [ProductController::class, 'suggestions']);
$router->get('/deals', [PageController::class, 'deals']);
$router->post('/newsletter', [PageController::class, 'newsletter']);

$router->get('/login', [AuthController::class, 'showLoginForm'], [GuestMiddleware::class]);
$router->post('/login', [AuthController::class, 'login'], [GuestMiddleware::class]);
$router->get('/register', [AuthController::class, 'showRegisterForm'], [GuestMiddleware::class]);
$router->post('/register', [AuthController::class, 'register'], [GuestMiddleware::class]);
$router->post('/logout', [AuthController::class, 'logout'], [AuthMiddleware::class]);

$router->get('/profile', [ProfileController::class, 'index'], [AuthMiddleware::class]);

$router->get('/cart', [CartController::class, 'index']);
$router->post('/cart/add', [CartController::class, 'add']);
$router->post('/cart/update', [CartController::class, 'update']);
$router->post('/cart/remove', [CartController::class, 'remove']);

$router->get('/wishlist', [WishlistController::class, 'index']);
$router->post('/wishlist/add', [WishlistController::class, 'add']);
$router->post('/wishlist/remove', [WishlistController::class, 'remove']);

$router->get('/compare', [CompareController::class, 'index']);
$router->post('/compare/add', [CompareController::class, 'add']);
$router->post('/compare/remove', [CompareController::class, 'remove']);
$router->post('/compare/clear', [CompareController::class, 'clear']);

$router->get('/checkout', [CheckoutController::class, 'index'], [AuthMiddleware::class]);
$router->post('/checkout/coupon', [CheckoutController::class, 'applyCoupon'], [AuthMiddleware::class]);
$router->post('/checkout/place-order', [CheckoutController::class, 'placeOrder'], [AuthMiddleware::class]);
$router->get('/checkout/success/{orderNumber}', [CheckoutController::class, 'success'], [AuthMiddleware::class]);

$router->get('/payment/{orderNumber}', [PaymentController::class, 'show'], [AuthMiddleware::class]);
$router->post('/payment/{orderNumber}', [PaymentController::class, 'process'], [AuthMiddleware::class]);

$router->get('/orders', [OrderController::class, 'index'], [AuthMiddleware::class]);
$router->get('/orders/{orderNumber}', [OrderController::class, 'show'], [AuthMiddleware::class]);
$router->get('/orders/{orderNumber}/invoice', [OrderController::class, 'invoice'], [AuthMiddleware::class]);

$router->get('/api/commerce/counts', static function (): void {
    header('Content-Type: application/json; charset=utf-8');
    header('Cache-Control: no-store');
    echo json_encode([
        'cart_count' => cart_count(),
        'cart_product_ids' => cart_product_ids(),
        'wishlist_count' => wishlist_count(),
        'compare_count' => compare_count(),
    ], JSON_THROW_ON_ERROR);
});

$router->group(['prefix' => '/admin'], static function ($router): void {
    $router->get('/login', [AdminAuthController::class, 'showLoginForm'], [AdminGuestMiddleware::class]);
    $router->post('/login', [AdminAuthController::class, 'login'], [AdminGuestMiddleware::class]);
    $router->post('/logout', [AdminAuthController::class, 'logout'], [AdminMiddleware::class]);

    $router->get('/dashboard', [AdminDashboardController::class, 'index'], [AdminMiddleware::class]);
    $router->get('/api/analytics', [AdminDashboardController::class, 'analyticsApi'], [AdminMiddleware::class]);

    $router->get('/products', [AdminProductController::class, 'index'], [AdminMiddleware::class]);
    $router->get('/products/create', [AdminProductController::class, 'create'], [AdminMiddleware::class]);
    $router->post('/products', [AdminProductController::class, 'store'], [AdminMiddleware::class]);
    $router->get('/products/{id}/edit', [AdminProductController::class, 'edit'], [AdminMiddleware::class]);
    $router->post('/products/{id}', [AdminProductController::class, 'update'], [AdminMiddleware::class]);
    $router->post('/products/{id}/delete', [AdminProductController::class, 'delete'], [AdminMiddleware::class]);

    $router->get('/categories', [AdminCategoryController::class, 'index'], [AdminMiddleware::class]);
    $router->get('/categories/create', [AdminCategoryController::class, 'create'], [AdminMiddleware::class]);
    $router->post('/categories', [AdminCategoryController::class, 'store'], [AdminMiddleware::class]);
    $router->get('/categories/{id}/edit', [AdminCategoryController::class, 'edit'], [AdminMiddleware::class]);
    $router->post('/categories/{id}', [AdminCategoryController::class, 'update'], [AdminMiddleware::class]);

    $router->get('/orders', [AdminOrderController::class, 'index'], [AdminMiddleware::class]);
    $router->get('/orders/{orderNumber}', [AdminOrderController::class, 'show'], [AdminMiddleware::class]);
    $router->post('/orders/{orderNumber}/status', [AdminOrderController::class, 'updateStatus'], [AdminMiddleware::class]);

    $router->get('/customers', [AdminCustomerController::class, 'index'], [AdminMiddleware::class]);
    $router->get('/customers/{id}', [AdminCustomerController::class, 'show'], [AdminMiddleware::class]);

    $router->get('/coupons', [AdminCouponController::class, 'index'], [AdminMiddleware::class]);
    $router->get('/coupons/create', [AdminCouponController::class, 'create'], [AdminMiddleware::class]);
    $router->post('/coupons', [AdminCouponController::class, 'store'], [AdminMiddleware::class]);
    $router->get('/coupons/{id}/edit', [AdminCouponController::class, 'edit'], [AdminMiddleware::class]);
    $router->post('/coupons/{id}', [AdminCouponController::class, 'update'], [AdminMiddleware::class]);

    $router->get('/banners', [AdminBannerController::class, 'index'], [AdminMiddleware::class]);
    $router->get('/banners/create', [AdminBannerController::class, 'create'], [AdminMiddleware::class]);
    $router->post('/banners', [AdminBannerController::class, 'store'], [AdminMiddleware::class]);
    $router->get('/banners/{id}/edit', [AdminBannerController::class, 'edit'], [AdminMiddleware::class]);
    $router->post('/banners/{id}', [AdminBannerController::class, 'update'], [AdminMiddleware::class]);
    $router->post('/banners/{id}/delete', [AdminBannerController::class, 'delete'], [AdminMiddleware::class]);

    $router->get('/reviews', [AdminReviewController::class, 'index'], [AdminMiddleware::class]);
    $router->post('/reviews/{id}/approve', [AdminReviewController::class, 'approve'], [AdminMiddleware::class]);
    $router->post('/reviews/{id}/reject', [AdminReviewController::class, 'reject'], [AdminMiddleware::class]);

    $router->get('/settings', [AdminSettingsController::class, 'index'], [AdminMiddleware::class]);
    $router->post('/settings', [AdminSettingsController::class, 'update'], [AdminMiddleware::class]);
});

$router->get('/health', static function (): void {
    $payload = [
        'status' => 'ok',
        'app' => env('APP_NAME', 'ShopQ'),
        'timestamp' => date('c'),
        'database' => 'disconnected',
    ];

    try {
        $db = Core\Database::getInstance();
        $count = (int) $db->fetch('SELECT COUNT(*) AS total FROM products')['total'];
        $payload['database'] = 'connected';
        $payload['products'] = $count;
    } catch (Throwable $exception) {
        $payload['status'] = 'degraded';
        $payload['database_error'] = $exception->getMessage();
    }

    header('Content-Type: application/json; charset=utf-8');
    header('Cache-Control: no-store');
    echo json_encode($payload, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE);
});
