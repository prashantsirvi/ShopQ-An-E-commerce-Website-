<?php if (!empty($breadcrumb)): ?>
<nav class="breadcrumb" aria-label="Breadcrumb">
    <ol>
        <?php foreach ($breadcrumb as $index => $item): ?>
            <li>
                <?php if (!empty($item['url'])): ?>
                    <a href="<?= e($item['url']) ?>"><?= e($item['label']) ?></a>
                <?php else: ?>
                    <span aria-current="page"><?= e($item['label']) ?></span>
                <?php endif; ?>
            </li>
        <?php endforeach; ?>
    </ol>
</nav>
<?php endif; ?>
