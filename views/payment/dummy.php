<section class="commerce-page payment-page">
    <div class="container payment-container">
        <div class="payment-card">
            <h1>Simulated Card Payment</h1>
            <p>Order <strong><?= e($order['order_number']) ?></strong> — <?= format_money((float) $order['total_amount']) ?></p>

            <form action="<?= url('/payment/' . $order['order_number']) ?>" method="POST" class="payment-form">
                <?= csrf_field() ?>
                <div class="form-group">
                    <label for="card_name">Name on card</label>
                    <input type="text" id="card_name" name="card_name" value="Demo Customer" required>
                </div>
                <div class="form-group">
                    <label for="card_number">Card number</label>
                    <input type="text" id="card_number" name="card_number" placeholder="4111 1111 1111 1111" required>
                </div>
                <div class="form-grid checkout-address-grid">
                    <div class="form-group">
                        <label for="expiry">Expiry</label>
                        <input type="text" id="expiry" name="expiry" placeholder="12/28" required>
                    </div>
                    <div class="form-group">
                        <label for="cvv">CVV</label>
                        <input type="text" id="cvv" name="cvv" placeholder="123" required>
                    </div>
                </div>
                <input type="hidden" name="simulate" value="success" id="simulate-field">
                <div class="payment-actions">
                    <button type="submit" class="btn btn-primary btn-lg" onclick="document.getElementById('simulate-field').value='success'">
                        Pay <?= format_money((float) $order['total_amount']) ?>
                    </button>
                    <button type="submit" class="btn btn-outline" onclick="document.getElementById('simulate-field').value='fail'">
                        Simulate Failure
                    </button>
                </div>
            </form>
        </div>
    </div>
</section>
