<section class="commerce-page payment-page">
    <div class="container payment-container">
        <div class="payment-card">
            <h1>Cash on Delivery</h1>
            <p>Your order <strong><?= e($order['order_number']) ?></strong> is confirmed. Pay <?= format_money((float) $order['total_amount']) ?> when your order arrives.</p>
            <a href="<?= url('/checkout/success/' . $order['order_number']) ?>" class="btn btn-primary">View Order</a>
        </div>
    </div>
</section>
