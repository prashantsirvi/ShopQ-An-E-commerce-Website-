<section class="commerce-page payment-page">
    <div class="container payment-container">
        <div class="payment-card">
            <h1>UPI Payment</h1>
            <p>Pay <?= format_money((float) $order['total_amount']) ?> for order <strong><?= e($order['order_number']) ?></strong></p>

            <div class="upi-box">
                <div class="upi-qr" aria-hidden="true">
                    <i class="fa-solid fa-qrcode"></i>
                </div>
                <p><strong>UPI ID:</strong> <?= e($upi_id) ?></p>
                <p class="summary-note">Scan with any UPI app or copy the UPI ID above.</p>
                <code class="upi-string"><?= e($upi_string) ?></code>
            </div>

            <form action="<?= url('/payment/' . $order['order_number']) ?>" method="POST" class="payment-form">
                <?= csrf_field() ?>
                <div class="form-group">
                    <label for="transaction_ref">UPI transaction reference *</label>
                    <input type="text" id="transaction_ref" name="transaction_ref" placeholder="e.g. 123456789012" required>
                </div>
                <button type="submit" class="btn btn-primary btn-lg btn-block">I have paid — Submit reference</button>
            </form>
        </div>
    </div>
</section>
