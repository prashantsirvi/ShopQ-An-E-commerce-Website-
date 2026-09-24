<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($title ?? 'ShopQ') ?></title>
    <link rel="stylesheet" href="<?= asset('css/app.css') ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer">
</head>
<body class="auth-body">
    <div class="auth-wrapper">
        <a href="<?= url('/') ?>" class="auth-brand">
            <span class="brand-icon"><i class="fa-solid fa-bag-shopping"></i></span>
            <span class="brand-text">ShopQ</span>
        </a>

        <?php require VIEWS_PATH . '/partials/flash.php'; ?>

        <div class="auth-card">
            <?= $content ?? '' ?>
        </div>
    </div>
</body>
</html>
