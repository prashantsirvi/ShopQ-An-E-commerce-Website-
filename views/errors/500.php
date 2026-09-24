<section class="error-page">
    <div class="container error-card">
        <h1>500</h1>
        <p><?= e($message ?? 'Internal server error.') ?></p>
        <a class="btn btn-primary" href="<?= url('/') ?>">Back to Home</a>
    </div>
</section>
