<h1>Admin Login</h1>
<p class="auth-subtitle">Secure access to ShopQ back-office.</p>

<form action="<?= url('/admin/login') ?>" method="POST" class="auth-form" novalidate>
    <?= csrf_field() ?>

    <div class="form-group">
        <label for="email">Admin email</label>
        <input
            type="email"
            id="email"
            name="email"
            value="<?= e($old['email'] ?? '') ?>"
            class="<?= isset($errors['email']) ? 'is-invalid' : '' ?>"
            required
            autocomplete="username"
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

    <button type="submit" class="btn btn-primary btn-block">Login to Admin</button>
</form>

<p class="auth-switch">
    <a href="<?= url('/') ?>">Back to storefront</a>
</p>

<div class="demo-credentials">
    <strong>Demo admin:</strong> admin@shopq.local / Admin@123
</div>
