<section class="profile-page">
    <div class="profile-card">
        <div class="profile-header">
            <div class="profile-avatar">
                <i class="fa-solid fa-user"></i>
            </div>
            <div>
                <h1><?= e(trim($user['first_name'] . ' ' . $user['last_name'])) ?></h1>
                <p><?= e($user['email']) ?></p>
            </div>
        </div>

        <div class="profile-grid">
            <div class="profile-item">
                <span class="label">Role</span>
                <strong><?= e($user['role_name']) ?></strong>
            </div>
            <div class="profile-item">
                <span class="label">Phone</span>
                <strong><?= e($user['phone'] ?: 'Not added') ?></strong>
            </div>
            <div class="profile-item">
                <span class="label">Member since</span>
                <strong><?= e(date('d M Y', strtotime($user['created_at']))) ?></strong>
            </div>
            <div class="profile-item">
                <span class="label">Last login</span>
                <strong><?= e($user['last_login_at'] ? date('d M Y, h:i A', strtotime($user['last_login_at'])) : 'First session') ?></strong>
            </div>
        </div>

        <p class="profile-note">
            <a href="<?= url('/orders') ?>" class="btn btn-primary">My Orders</a>
            <a href="<?= url('/wishlist') ?>" class="btn btn-outline">Wishlist</a>
        </p>
    </div>
</section>
