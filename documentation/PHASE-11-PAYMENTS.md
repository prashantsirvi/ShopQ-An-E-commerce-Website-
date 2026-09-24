# Phase 11 — Payments

**Status:** Complete

## Features

- Payment gateway interface (`Services\PaymentGatewayInterface`)
- Gateways: **COD**, **Dummy Card**, **UPI QR**, **Razorpay stub**
- Payment records in `payments` table
- Checkout redirects to `/payment/{orderNumber}` for online methods

## What to Test

1. Checkout → select **Simulated Card Payment** → place order
2. Enter card `4111111111111111` → **Pay** → success page
3. Repeat with **Simulate Failure** → error message, order stays pending
4. Checkout → **UPI** → scan/copy UPI ID → enter reference `1234567890` → submit
5. **COD** still goes directly to success page
6. Check `payments` table for transaction IDs
