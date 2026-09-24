# Phase 9 — Compare Products

**Status:** Complete  
**Date:** July 23, 2026

## Features

| Feature | Route / Method |
|---------|----------------|
| Compare table | `GET /compare` |
| Add product | `POST /compare/add` |
| Remove product | `POST /compare/remove` |
| Clear all | `POST /compare/clear` |

## Behavior

- Maximum **4 products** in compare list
- Session storage for guests, `compare_items` table for logged-in users
- Compare icon on product detail page
- Header compare badge (desktop)
- Side-by-side table: price, category, brand, rating, stock, description

## What to Test

1. Open a product detail page → click **compare** (scale icon) → toast + header badge.
2. Repeat for 2–3 different products.
3. Visit [http://localhost/shopq/public/compare](http://localhost/shopq/public/compare) → table shows all products.
4. Try adding a **5th product** → error message about 4-item limit.
5. Click **Remove** on one column → table updates.
6. Click **Clear All** → empty state appears.
7. From compare table, click **Add to Cart** → item lands in cart.
8. **Guest → login merge:** add compare items as guest → login → compare list preserved.

## Next Phase

**Phase 10 — Checkout**
