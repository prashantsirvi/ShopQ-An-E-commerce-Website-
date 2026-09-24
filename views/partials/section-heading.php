<?php

use Core\View;

$title = $title ?? 'Section';
$subtitle = $subtitle ?? '';
$link = $link ?? null;
$linkText = $linkText ?? 'View all';
?>
<div class="section-heading">
    <div>
        <h2><?= e($title) ?></h2>
        <?php if ($subtitle !== ''): ?>
            <p><?= e($subtitle) ?></p>
        <?php endif; ?>
    </div>
    <?php if ($link): ?>
        <a href="<?= e($link) ?>" class="section-link"><?= e($linkText) ?> <i class="fa-solid fa-arrow-right"></i></a>
    <?php endif; ?>
</div>
