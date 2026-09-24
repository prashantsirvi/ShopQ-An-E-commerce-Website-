<?php use Core\View; ?>

<section class="hero-banner">
    <div class="container hero-banner-grid">
        <div class="hero-banner-copy">
            <span class="badge badge-accent">New Season Collection</span>
            <h1>Discover products you’ll love at prices that feel right.</h1>
            <p><?= e(setting('site_tagline')) ?></p>
            <div class="hero-actions">
                <a class="btn btn-primary" href="<?= url('/products') ?>">Shop Now</a>
                <a class="btn btn-outline" href="<?= url('/deals') ?>">Explore Deals</a>
            </div>
            <div class="hero-trust">
                <span><i class="fa-solid fa-shield-halved"></i> Secure checkout</span>
                <span><i class="fa-solid fa-rotate-left"></i> Easy returns</span>
                <span><i class="fa-solid fa-bolt"></i> Fast delivery</span>
            </div>
        </div>
        <div class="hero-banner-card">
            <div class="promo-card promo-primary">
                <span class="promo-label">Flash Sale Live</span>
                <h2>Up to 50% off</h2>
                <p>Limited-time offers on top picks across electronics, fashion, and more.</p>
                <a href="<?= url('/deals') ?>" class="btn btn-light btn-sm">Grab Deals</a>
            </div>
            <div class="promo-card promo-secondary">
                <span class="promo-label">Member Perks</span>
                <h3>Extra 10% off with ShopQ Gold</h3>
                <a href="<?= url('/register') ?>">Join free →</a>
            </div>
        </div>
    </div>
</section>

<section class="home-section">
    <div class="container">
        <?php View::partial('partials/section-heading', [
            'title' => 'Shop by Category',
            'subtitle' => 'Browse curated collections across every lifestyle.',
        ]); ?>
        <div class="category-grid">
            <?php foreach ($categories as $category): ?>
                <?php View::partial('partials/category-card', ['category' => $category]); ?>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<?php if (!empty($flashSaleProducts)): ?>
<section class="home-section flash-sale-section">
    <div class="container">
        <?php View::partial('partials/section-heading', [
            'title' => 'Flash Sale',
            'subtitle' => 'Hurry — prices go up when the timer ends.',
            'link' => url('/deals'),
            'linkText' => 'See all deals',
        ]); ?>
        <div class="flash-sale-bar">
            <div class="flash-timer" data-flash-timer data-end="<?= e(date('c', strtotime('+2 days'))) ?>">
                <span class="timer-label">Ends in</span>
                <span class="timer-value" data-timer-display>02:00:00:00</span>
            </div>
        </div>
        <div class="product-grid product-grid-compact">
            <?php foreach ($flashSaleProducts as $product): ?>
                <?php View::partial('partials/product-card', ['product' => $product]); ?>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<section class="home-section">
    <div class="container">
        <?php View::partial('partials/section-heading', [
            'title' => 'Featured Products',
            'subtitle' => 'Hand-picked items with top ratings and premium quality.',
            'link' => url('/products'),
        ]); ?>
        <div class="product-grid" data-product-grid>
            <?php foreach ($featuredProducts as $product): ?>
                <?php View::partial('partials/product-card', ['product' => $product]); ?>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="home-section section-muted">
    <div class="container">
        <?php View::partial('partials/section-heading', [
            'title' => 'Best Sellers',
            'subtitle' => 'Most loved by ShopQ customers this month.',
            'link' => url('/products'),
        ]); ?>
        <div class="product-grid">
            <?php foreach ($bestSellers as $product): ?>
                <?php View::partial('partials/product-card', ['product' => $product]); ?>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="home-section">
    <div class="container">
        <?php View::partial('partials/section-heading', [
            'title' => 'Trending Now',
            'subtitle' => 'Popular picks based on views and recent orders.',
            'link' => url('/products'),
        ]); ?>
        <div class="product-grid">
            <?php foreach ($trendingProducts as $product): ?>
                <?php View::partial('partials/product-card', ['product' => $product]); ?>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="home-section">
    <div class="container">
        <div class="newsletter-card">
            <div>
                <h2>Stay ahead of the deals</h2>
                <p>Get early access to flash sales, new arrivals, and exclusive coupons.</p>
            </div>
            <form class="newsletter-form" action="<?= url('/newsletter') ?>" method="POST">
                <?= csrf_field() ?>
                <input type="email" name="email" placeholder="Enter your email" required>
                <button type="submit" class="btn btn-primary">Subscribe</button>
            </form>
        </div>
    </div>
</section>

<section class="home-section skeleton-demo-section" hidden data-skeleton-demo>
    <div class="container">
        <?php View::partial('partials/section-heading', [
            'title' => 'Loading preview',
            'subtitle' => 'Skeleton state used while products fetch via AJAX.',
        ]); ?>
        <div class="product-grid">
            <?php for ($i = 0; $i < 4; $i++): ?>
                <?php View::partial('partials/product-card-skeleton'); ?>
            <?php endfor; ?>
        </div>
    </div>
</section>
