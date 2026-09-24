# Phase 7 — Shopping Cart

**Status:** Complete  
**Date:** July 23, 2026

## Features

| Feature | Route / Method |
|---------|----------------|
| View cart | `GET /cart` |
| Add item | `POST /cart/add` |
| Update quantity | `POST /cart/update` |
| Remove item | `POST /cart/remove` |
| Commerce counts API | `GET /api/commerce/counts` |

## Behavior

- **Guest users:** cart stored in session (`guest_cart`)
- **Logged-in customers:** cart stored in `carts` + `cart_items` tables
- **On login/register:** guest cart merges into the user cart
- **Header badge:** live cart count
- **Product detail:** AJAX add to cart with variant + quantity support

## What to Test

1. Open any product → click **Add to Cart** → confirm toast and header badge increase.
2. Visit [http://localhost/shopq/public/cart](http://localhost/shopq/public/cart) → item appears with image, price, quantity.
3. Change quantity → click **Update** → line total and subtotal refresh.
4. Click trash icon → item removed.
5. **Guest flow:** add items without login → cart persists on refresh.
6. **Login merge:** add items as guest → login as `customer@shopq.local` / `Admin@123` → cart items still present.
7. Click **Proceed to Checkout** while logged in → redirects to checkout (Phase 10).
8. Click **Login to Checkout** as guest → redirects to login.

## Next Phase

**Phase 8 — Wishlist**
