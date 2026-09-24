<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice <?= e($order['order_number']) ?> | <?= e(setting('site_name', 'ShopQ')) ?></title>
    <style>
        body { font-family: Inter, Arial, sans-serif; color: #111; margin: 2rem; }
        .invoice-header { display: flex; justify-content: space-between; margin-bottom: 2rem; }
        table { width: 100%; border-collapse: collapse; margin: 1.5rem 0; }
        th, td { border-bottom: 1px solid #ddd; padding: 0.65rem; text-align: left; }
        .totals { max-width: 320px; margin-left: auto; }
        .totals div { display: flex; justify-content: space-between; padding: 0.35rem 0; }
        @media print { .no-print { display: none; } }
    </style>
</head>
<body>
    <div class="no-print" style="margin-bottom:1rem;">
        <button onclick="window.print()">Print Invoice</button>
    </div>

    <div class="invoice-header">
        <div>
            <h1><?= e(setting('site_name', 'ShopQ')) ?></h1>
            <p>Tax Invoice</p>
        </div>
        <div>
            <strong>Invoice #<?= e($order['order_number']) ?></strong><br>
            Date: <?= e(date('d M Y', strtotime($order['placed_at']))) ?>
        </div>
    </div>

    <p><strong>Bill To:</strong><br>
    <?= e($order['shipping_name']) ?><br>
    <?= e($order['shipping_phone']) ?><br>
    <?= e($order['shipping_address']) ?><br>
    <?= e($order['shipping_city']) ?>, <?= e($order['shipping_state']) ?> — <?= e($order['shipping_postal_code']) ?></p>

    <table>
        <thead>
            <tr>
                <th>Item</th>
                <th>SKU</th>
                <th>Qty</th>
                <th>Unit Price</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($order['items'] as $item): ?>
                <tr>
                    <td><?= e($item['product_name']) ?><?= $item['variant_label'] ? ' (' . e($item['variant_label']) . ')' : '' ?></td>
                    <td><?= e($item['sku']) ?></td>
                    <td><?= (int) $item['quantity'] ?></td>
                    <td><?= format_money((float) $item['unit_price']) ?></td>
                    <td><?= format_money((float) $item['total_price']) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <div class="totals">
        <div><span>Subtotal</span><span><?= format_money((float) $order['subtotal']) ?></span></div>
        <?php if ($order['discount_amount'] > 0): ?>
            <div><span>Discount</span><span>−<?= format_money((float) $order['discount_amount']) ?></span></div>
        <?php endif; ?>
        <div><span>Shipping</span><span><?= $order['shipping_amount'] > 0 ? format_money((float) $order['shipping_amount']) : 'Free' ?></span></div>
        <div><span>Tax</span><span><?= format_money((float) $order['tax_amount']) ?></span></div>
        <div><strong>Total</strong><strong><?= format_money((float) $order['total_amount']) ?></strong></div>
    </div>

    <p>Payment: <?= e(strtoupper($order['payment_method'])) ?> — <?= e(ucfirst($order['payment_status'])) ?></p>
</body>
</html>
