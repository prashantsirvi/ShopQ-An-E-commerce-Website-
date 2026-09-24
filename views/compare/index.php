<?php use Core\View; ?>

<section class="commerce-page compare-page">
    <div class="container">
        <?php View::partial('partials/breadcrumb', ['breadcrumb' => [
            ['label' => 'Home', 'url' => url('/')],
            ['label' => 'Compare', 'url' => null],
        ]]); ?>

        <div class="commerce-header compare-header">
            <div>
                <h1>Compare Products</h1>
                <p>Compare up to <?= (int) $maxItems ?> products side by side.</p>
            </div>
            <?php if ($items !== []): ?>
                <form action="<?= url('/compare/clear') ?>" method="POST">
                    <?= csrf_field() ?>
                    <input type="hidden" name="redirect" value="<?= e(url('/compare')) ?>">
                    <button type="submit" class="btn btn-outline">Clear All</button>
                </form>
            <?php endif; ?>
        </div>

        <?php if ($items === []): ?>
            <div class="empty-state">
                <i class="fa-solid fa-scale-balanced"></i>
                <h2>No products to compare</h2>
                <p>Add products from the detail page using the compare icon.</p>
                <a href="<?= url('/products') ?>" class="btn btn-primary">Browse Products</a>
            </div>
        <?php else: ?>
            <div class="compare-table-wrap">
                <table class="compare-table">
                    <thead>
                        <tr>
                            <th>Feature</th>
                            <?php foreach ($items as $product): ?>
                                <th>
                                    <div class="compare-product-head">
                                        <img src="<?= e($product['image_url']) ?>" alt="<?= e($product['name']) ?>">
                                        <a href="<?= url('/products/' . $product['slug']) ?>"><?= e($product['name']) ?></a>
                                        <form action="<?= url('/compare/remove') ?>" method="POST">
                                            <?= csrf_field() ?>
                                            <input type="hidden" name="product_id" value="<?= (int) $product['id'] ?>">
                                            <input type="hidden" name="redirect" value="<?= e(url('/compare')) ?>">
                                            <button type="submit" class="btn btn-outline btn-sm">Remove</button>
                                        </form>
                                    </div>
                                </th>
                            <?php endforeach; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Price</td>
                            <?php foreach ($items as $product): ?>
                                <td><strong><?= format_money((float) $product['display_price']) ?></strong></td>
                            <?php endforeach; ?>
                        </tr>
                        <tr>
                            <td>Category</td>
                            <?php foreach ($items as $product): ?>
                                <td><?= e($product['category_name']) ?></td>
                            <?php endforeach; ?>
                        </tr>
                        <tr>
                            <td>Brand</td>
                            <?php foreach ($items as $product): ?>
                                <td><?= e($product['brand_name'] ?? '—') ?></td>
                            <?php endforeach; ?>
                        </tr>
                        <tr>
                            <td>Rating</td>
                            <?php foreach ($items as $product): ?>
                                <td><?= number_format((float) $product['rating_avg'], 1) ?> (<?= (int) $product['rating_count'] ?>)</td>
                            <?php endforeach; ?>
                        </tr>
                        <tr>
                            <td>Stock</td>
                            <?php foreach ($items as $product): ?>
                                <td><?= (int) $product['stock'] > 0 ? 'In stock' : 'Out of stock' ?></td>
                            <?php endforeach; ?>
                        </tr>
                        <tr>
                            <td>Description</td>
                            <?php foreach ($items as $product): ?>
                                <td><?= e(mb_strimwidth($product['short_description'] ?? '', 0, 120, '…')) ?></td>
                            <?php endforeach; ?>
                        </tr>
                        <tr>
                            <td>Action</td>
                            <?php foreach ($items as $product): ?>
                                <td>
                                    <form action="<?= url('/cart/add') ?>" method="POST">
                                        <?= csrf_field() ?>
                                        <input type="hidden" name="product_id" value="<?= (int) $product['id'] ?>">
                                        <input type="hidden" name="quantity" value="1">
                                        <input type="hidden" name="redirect" value="<?= e(url('/cart')) ?>">
                                        <button type="submit" class="btn btn-primary btn-sm">Add to Cart</button>
                                    </form>
                                </td>
                            <?php endforeach; ?>
                        </tr>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</section>
