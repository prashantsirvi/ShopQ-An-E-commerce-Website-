<section class="error-page">
    <div class="container error-card">
        <h1>404</h1>
        <p><?= e($message ?? 'Page not found.') ?></p>
        <a class="btn btn-primary" href="<?= url('/') ?>">Back to Home</a>
    </div>
</section>
