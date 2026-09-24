# Phase 8 — Wishlist

**Status:** Complete  
**Date:** July 23, 2026

## Features

| Feature | Route / Method |
|---------|----------------|
| View wishlist | `GET /wishlist` |
| Add product | `POST /wishlist/add` |
| Remove product | `POST /wishlist/remove` |

## Behavior

- **Guest users:** product IDs stored in session
- **Logged-in customers:** stored in `wishlists` table
- **On login/register:** session wishlist merges into database
- Heart icon on product cards and product detail page (AJAX)
- Wishlist page: remove item or add to cart directly

## What to Test

1. On home or `/products`, click the **heart** on a product card → toast + header wishlist badge updates.
2. Visit [http://localhost/shopq/public/wishlist](http://localhost/shopq/public/wishlist) → saved products appear.
3. Click **Remove** → product disappears from wishlist.
4. Click **Add to Cart** from wishlist → item appears in cart.
5. **Guest:** add 2–3 items → refresh page → items remain.
6. **Login merge:** add items as guest → login → wishlist items still visible.
7. Click heart again on same product → no duplicate (silent ignore).

## Next Phase

**Phase 9 — Compare Products**
