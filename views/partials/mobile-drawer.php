<div class="mobile-drawer" data-mobile-drawer hidden>
    <div class="mobile-drawer-backdrop" data-mobile-close></div>
    <aside class="mobile-drawer-panel" aria-label="Mobile navigation">
        <div class="mobile-drawer-header">
            <strong><?= e(setting('site_name', 'ShopQ')) ?></strong>
            <button type="button" class="icon-btn" aria-label="Close menu" data-mobile-close>
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
        <nav class="mobile-nav">
            <a href="<?= url('/') ?>">Home</a>
            <a href="<?= url('/products') ?>">Products</a>
            <a href="<?= url('/deals') ?>">Deals</a>
            <a href="<?= url('/cart') ?>">Cart</a>
            <a href="<?= url('/wishlist') ?>">Wishlist</a>
            <?php if (is_customer_authenticated()): ?>
                <a href="<?= url('/profile') ?>">My Profile</a>
            <?php else: ?>
                <a href="<?= url('/login') ?>">Login</a>
                <a href="<?= url('/register') ?>">Register</a>
            <?php endif; ?>
        </nav>
    </aside>
</div>
