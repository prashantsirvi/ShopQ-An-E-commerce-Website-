# Phase 16 — Testing & Documentation

**Status:** Complete

## Smoke Tests

Run all tests:

```powershell
C:\xampp\php\php.exe C:\xampp\htdocs\shopq\tests\run-all.php
```

Individual tests in `/tests`:

| File | Checks |
|------|--------|
| `health-smoke.php` | DB connection |
| `route-smoke.php` | Core routes |
| `home-smoke.php` | Home page |
| `products-smoke.php` | Catalog |
| `commerce-smoke.php` | Cart, wishlist, compare |
| `orders-smoke.php` | Auth guard on orders |

## Manual Test Checklist

### Customer Flow
- [ ] Register / login
- [ ] Browse, search, filter products
- [ ] Add to cart, wishlist, compare
- [ ] Checkout with coupon
- [ ] Pay via COD, dummy card, UPI
- [ ] View orders + invoice

### Admin Flow
- [ ] Admin login
- [ ] Dashboard charts load
- [ ] CRUD product/category/coupon/banner
- [ ] Update order status
- [ ] Approve review (if any exist)
- [ ] Save settings

## What to Test

1. Run `run-all.php` → all PASS
2. Complete manual checklist above
3. Review all docs in `/documentation`
