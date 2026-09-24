<footer class="site-footer">
    <div class="container footer-grid">
        <div class="footer-brand">
            <a href="<?= url('/') ?>" class="brand">
                <span class="brand-icon"><i class="fa-solid fa-bag-shopping"></i></span>
                <span class="brand-text"><?= e(setting('site_name', 'ShopQ')) ?></span>
            </a>
            <p><?= e(setting('site_tagline', 'Your modern multi-category shopping destination')) ?></p>
            <div class="footer-social">
                <a href="#" aria-label="Facebook"><i class="fa-brands fa-facebook"></i></a>
                <a href="#" aria-label="Instagram"><i class="fa-brands fa-instagram"></i></a>
                <a href="#" aria-label="Twitter"><i class="fa-brands fa-x-twitter"></i></a>
                <a href="#" aria-label="YouTube"><i class="fa-brands fa-youtube"></i></a>
            </div>
        </div>

        <div class="footer-column">
            <h3>Shop</h3>
            <a href="<?= url('/products') ?>">All Products</a>
            <a href="<?= url('/deals') ?>">Deals</a>
            <a href="<?= url('/products') ?>">New Arrivals</a>
            <a href="<?= url('/deals') ?>">Flash Sale</a>
        </div>

        <div class="footer-column">
            <h3>Customer</h3>
            <a href="<?= url('/profile') ?>">My Account</a>
            <a href="<?= url('/cart') ?>">Cart</a>
            <a href="<?= url('/wishlist') ?>">Wishlist</a>
            <a href="<?= url('/orders') ?>">Orders</a>
        </div>

        <div class="footer-column">
            <h3>Support</h3>
            <a href="mailto:<?= e(setting('support_email', 'support@shopq.local')) ?>">Email Support</a>
            <span><?= e(setting('support_phone', '1800-000-0000')) ?></span>
            <a href="#">Shipping Policy</a>
            <a href="#">Return Policy</a>
        </div>
    </div>

    <div class="footer-bottom">
        <div class="container footer-bottom-inner">
            <p>&copy; <?= date('Y') ?> <?= e(setting('site_name', 'ShopQ')) ?>. All rights reserved.</p>
            <div class="payment-icons" aria-label="Accepted payment methods">
                <span><i class="fa-solid fa-credit-card"></i> Cards</span>
                <span><i class="fa-solid fa-money-bill-wave"></i> COD</span>
                <span><i class="fa-solid fa-qrcode"></i> UPI</span>
            </div>
        </div>
    </div>
</footer>
