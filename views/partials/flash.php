<?php if (!empty($_SESSION['_flash'])): ?>
    <div class="flash-stack" aria-live="polite">
        <?php foreach ($_SESSION['_flash'] as $type => $message): ?>
            <div class="flash flash-<?= e($type) ?>">
                <?= e($message) ?>
            </div>
        <?php endforeach; ?>
    </div>
    <?php unset($_SESSION['_flash']); ?>
<?php endif; ?>
