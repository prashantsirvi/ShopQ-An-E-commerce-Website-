<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="app-url" content="<?= e(env('APP_URL', url('/'))) ?>">
    <title><?= e($title ?? 'Admin') ?> | ShopQ Admin</title>
    <link rel="stylesheet" href="<?= asset('css/app.css') ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer">
</head>
<body class="admin-body">
    <header class="admin-header">
        <div class="admin-header-inner">
            <a href="<?= url('/admin/dashboard') ?>" class="brand">
                <span class="brand-icon"><i class="fa-solid fa-gauge-high"></i></span>
                <span class="brand-text">ShopQ Admin</span>
            </a>
            <?php if ($admin = auth_admin()): ?>
                <div class="admin-user">
                    <span><?= e($admin['name']) ?> (<?= e($admin['role_slug']) ?>)</span>
                    <form action="<?= url('/admin/logout') ?>" method="POST" class="inline-form">
                        <?= csrf_field() ?>
                        <button type="submit" class="btn btn-outline btn-sm">Logout</button>
                    </form>
                </div>
            <?php endif; ?>
        </div>
    </header>

    <div class="admin-shell">
        <?php require VIEWS_PATH . '/partials/admin-sidebar.php'; ?>
        <main class="admin-main">
            <?php require VIEWS_PATH . '/partials/flash.php'; ?>
            <?= $content ?? '' ?>
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    <script src="<?= asset('js/admin.js') ?>"></script>
</body>
</html>
