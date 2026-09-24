<section class="checkout-panel">
    <h2>Payment Method</h2>
    <div class="payment-options">
        <label class="payment-option">
            <input type="radio" name="payment_method" value="cod" checked>
            <span>Cash on Delivery (COD)</span>
        </label>
        <label class="payment-option">
            <input type="radio" name="payment_method" value="dummy">
            <span>Simulated Card Payment (demo)</span>
        </label>
        <label class="payment-option">
            <input type="radio" name="payment_method" value="upi">
            <span>UPI QR + reference</span>
        </label>
    </div>

    <div class="form-group">
        <label for="notes">Order notes (optional)</label>
        <textarea id="notes" name="notes" rows="3" placeholder="Delivery instructions"></textarea>
    </div>

    <button type="submit" class="btn btn-primary btn-lg btn-block">Place Order — <?= format_money((float) $summary['total']) ?></button>
</section>
