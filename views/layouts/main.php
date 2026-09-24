<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?= e($metaDescription ?? setting('site_tagline', 'ShopQ — Modern shopping platform')) ?>">
    <meta name="app-url" content="<?= e(env('APP_URL', url('/'))) ?>">
    <meta name="csrf-token" content="<?= e(csrf_token()) ?>">
    <meta name="csrf-field" content="<?= e(csrf_token_name()) ?>">
    <title><?= e($title ?? setting('site_name', 'ShopQ')) ?> | <?= e(setting('site_name', 'ShopQ')) ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= asset('css/app.css') ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer">
    <script>
        (function () {
            var theme = localStorage.getItem('shopq_theme') || 'light';
            document.documentElement.setAttribute('data-theme', theme);
        })();
    </script>
</head>
<body>
    <?php require VIEWS_PATH . '/partials/header.php'; ?>

    <main class="site-main">
        <div class="container flash-container">
            <?php require VIEWS_PATH . '/partials/flash.php'; ?>
        </div>
        <?= $content ?? '' ?>
    </main>

    <?php require VIEWS_PATH . '/partials/footer.php'; ?>

    <script src="<?= asset('js/app.js') ?>"></script>
    <script src="<?= asset('js/commerce.js') ?>"></script>
    <script src="<?= asset('js/products.js') ?>"></script>
</body>
</html>
