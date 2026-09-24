# Phase 10 — Checkout

**Status:** Complete  
**Date:** July 23, 2026

## Features

| Feature | Route / Method |
|---------|----------------|
| Checkout page | `GET /checkout` (login required) |
| Apply coupon | `POST /checkout/coupon` |
| Place order | `POST /checkout/place-order` |
| Success page | `GET /checkout/success/{orderNumber}` |

## Behavior

- Login required (guests redirected to login with return URL)
- Delivery address form saved to `addresses` table
- Order summary: subtotal, coupon discount, shipping, tax, total
- **Coupons (seed data):** `WELCOME10`, `FLAT100`, `MEGA20`
- Free shipping above ₹999 (configurable via settings)
- Payment methods: COD (works), UPI/Card placeholders for Phase 11
- Order created in `orders` + `order_items`, stock decremented, cart cleared
- COD orders get status `confirmed`

## What to Test

1. Add products to cart (total ideally above ₹499 for coupon testing).
2. Go to [http://localhost/shopq/public/checkout](http://localhost/shopq/public/checkout) **without login** → redirected to login.
3. Login as `customer@shopq.local` / `Admin@123` → checkout loads.
4. Fill delivery address form → select **Cash on Delivery** → click **Place Order**.
5. Success page shows order number, items, and total.
6. Return to `/cart` → cart should be **empty**.
7. Apply coupon **WELCOME10** (min order ₹499) → discount appears in summary → place another order.
8. Try **FLAT100** on order above ₹999.
9. Order above ₹999 → shipping shows **Free**.
10. Check database: `orders`, `order_items`, `order_status_history` have new rows.

## Seed Coupon Reference

| Code | Type | Value | Min Order |
|------|------|-------|-----------|
| WELCOME10 | 10% | max ₹200 | ₹499 |
| FLAT100 | ₹100 flat | — | ₹999 |
| MEGA20 | 20% | max ₹500 | ₹1499 |

## Next Phase

**Phase 11 — Payments:** Razorpay/UPI integration, payment webhooks, order payment status updates.
