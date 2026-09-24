<?php

use Core\View;
?>
<header class="site-header">
    <div class="topbar">
        <div class="container topbar-inner">
            <p><?= e(setting('site_tagline', 'Your modern multi-category shopping destination')) ?></p>
            <div class="topbar-links">
                <span><i class="fa-solid fa-truck-fast"></i> Free shipping above <?= format_money((float) setting('free_shipping_min', 999)) ?></span>
                <span><i class="fa-solid fa-headset"></i> <?= e(setting('support_phone', '1800-000-0000')) ?></span>
            </div>
        </div>
    </div>

    <div class="container header-inner">
        <div class="header-left">
            <button class="icon-btn mobile-menu-toggle" type="button" aria-label="Open menu" data-mobile-toggle>
                <i class="fa-solid fa-bars"></i>
            </button>

            <a href="<?= url('/') ?>" class="brand">
                <span class="brand-icon"><i class="fa-solid fa-bag-shopping"></i></span>
                <span class="brand-text"><?= e(setting('site_name', 'ShopQ')) ?></span>
            </a>
        </div>

        <div class="header-center">
            <?php View::partial('partials/search-bar'); ?>
        </div>

        <div class="header-actions">
            <button class="icon-btn theme-toggle" type="button" aria-label="Toggle dark mode" data-theme-toggle>
                <i class="fa-solid fa-moon"></i>
            </button>

            <a href="<?= url('/wishlist') ?>" class="icon-btn header-icon-link" aria-label="Wishlist">
                <i class="fa-regular fa-heart"></i>
                <span class="badge-count" data-wishlist-count><?= wishlist_count() ?: '' ?></span>
            </a>

            <a href="<?= url('/compare') ?>" class="icon-btn header-icon-link hide-mobile" aria-label="Compare">
                <i class="fa-solid fa-scale-balanced"></i>
                <span class="badge-count" data-compare-count><?= compare_count() ?: '' ?></span>
            </a>

            <a href="<?= url('/cart') ?>" class="icon-btn header-icon-link" aria-label="Cart">
                <i class="fa-solid fa-cart-shopping"></i>
                <span class="badge-count" data-cart-count><?= cart_count() ?: '' ?></span>
            </a>

            <?php if ($customer = auth_customer()): ?>
                <div class="header-user">
                    <a href="<?= url('/orders') ?>" class="header-link hide-mobile">Orders</a>
                    <a href="<?= url('/profile') ?>" class="header-link">
                        <i class="fa-solid fa-user"></i>
                        <span class="header-user-name"><?= e($customer['name']) ?></span>
                    </a>
                    <form action="<?= url('/logout') ?>" method="POST" class="inline-form">
                        <?= csrf_field() ?>
                        <button type="submit" class="btn btn-outline btn-sm">Logout</button>
                    </form>
                </div>
            <?php else: ?>
                <a href="<?= url('/login') ?>" class="header-link hide-mobile">Login</a>
                <a href="<?= url('/register') ?>" class="btn btn-primary btn-sm hide-mobile">Register</a>
            <?php endif; ?>
        </div>
    </div>

    <nav class="main-nav" aria-label="Primary">
        <div class="container">
            <a href="<?= url('/') ?>" class="nav-link <?= is_active_nav('/') ?>">Home</a>
            <a href="<?= url('/products') ?>" class="nav-link <?= is_active_nav('/products') ?>">Products</a>
            <a href="<?= url('/deals') ?>" class="nav-link <?= is_active_nav('/deals') ?>">Deals</a>
            <a href="<?= url('/products') ?>" class="nav-link">New Arrivals</a>
            <a href="<?= url('/deals') ?>" class="nav-link nav-link-accent">Flash Sale</a>
        </div>
    </nav>

    <?php View::partial('partials/mobile-drawer'); ?>
</header>
