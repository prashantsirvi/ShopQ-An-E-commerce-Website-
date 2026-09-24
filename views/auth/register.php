<h1>Create your account</h1>
<p class="auth-subtitle">Join ShopQ for a faster checkout and personalized shopping.</p>

<form action="<?= url('/register') ?>" method="POST" class="auth-form" novalidate>
    <?= csrf_field() ?>

    <div class="form-row">
        <div class="form-group">
            <label for="first_name">First name</label>
            <input
                type="text"
                id="first_name"
                name="first_name"
                value="<?= e($old['first_name'] ?? '') ?>"
                class="<?= isset($errors['first_name']) ? 'is-invalid' : '' ?>"
                required
            >
            <?php if (!empty($errors['first_name'])): ?>
                <span class="form-error"><?= e($errors['first_name']) ?></span>
            <?php endif; ?>
        </div>

        <div class="form-group">
            <label for="last_name">Last name</label>
            <input
                type="text"
                id="last_name"
                name="last_name"
                value="<?= e($old['last_name'] ?? '') ?>"
                class="<?= isset($errors['last_name']) ? 'is-invalid' : '' ?>"
                required
            >
            <?php if (!empty($errors['last_name'])): ?>
                <span class="form-error"><?= e($errors['last_name']) ?></span>
            <?php endif; ?>
        </div>
    </div>

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
        <label for="phone">Mobile number (optional)</label>
        <input
            type="tel"
            id="phone"
            name="phone"
            value="<?= e($old['phone'] ?? '') ?>"
            class="<?= isset($errors['phone']) ? 'is-invalid' : '' ?>"
            placeholder="10-digit mobile number"
        >
        <?php if (!empty($errors['phone'])): ?>
            <span class="form-error"><?= e($errors['phone']) ?></span>
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
            autocomplete="new-password"
        >
        <?php if (!empty($errors['password'])): ?>
            <span class="form-error"><?= e($errors['password']) ?></span>
        <?php endif; ?>
        <small class="form-hint">Minimum 8 characters with at least one letter and one number.</small>
    </div>

    <div class="form-group">
        <label for="password_confirmation">Confirm password</label>
        <input
            type="password"
            id="password_confirmation"
            name="password_confirmation"
            class="<?= isset($errors['password_confirmation']) ? 'is-invalid' : '' ?>"
            required
            autocomplete="new-password"
        >
        <?php if (!empty($errors['password_confirmation'])): ?>
            <span class="form-error"><?= e($errors['password_confirmation']) ?></span>
        <?php endif; ?>
    </div>

    <button type="submit" class="btn btn-primary btn-block">Create Account</button>
</form>

<p class="auth-switch">
    Already have an account?
    <a href="<?= url('/login') ?>">Login</a>
</p>
