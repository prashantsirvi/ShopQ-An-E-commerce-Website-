<h1>Login to ShopQ</h1>
<p class="auth-subtitle">Access your orders, wishlist, and saved addresses.</p>

<form action="<?= url('/login') ?>" method="POST" class="auth-form" novalidate>
    <?= csrf_field() ?>

    <div class="form-group">
        <label for="email">Email address</label>
        <input
            type="email"
            id="email"
            name="email"
            value="<?= e($old['email'] ?? '') ?>"
            class="<?= isset($errors['email']) ? 'is-invalid' : '' ?>"
            required
            autocomplete="email"
        >
        <?php if (!empty($errors['email'])): ?>
            <span class="form-error"><?= e($errors['email']) ?></span>
        <?php endif; ?>
    </div>

    <div class="form-group">
        <label for="password">Password</label>
        <input
            type="password"
            id="password"
            name="password"
            class="<?= isset($errors['password']) ? 'is-invalid' : '' ?>"
            required
            autocomplete="current-password"
        >
        <?php if (!empty($errors['password'])): ?>
            <span class="form-error"><?= e($errors['password']) ?></span>
        <?php endif; ?>
    </div>

    <button type="submit" class="btn btn-primary btn-block">Login</button>
</form>

<p class="auth-switch">
    New to ShopQ?
    <a href="<?= url('/register') ?>">Create an account</a>
</p>

<p class="auth-switch">
    Staff member?
    <a href="<?= url('/admin/login') ?>">Admin login</a>
</p>
