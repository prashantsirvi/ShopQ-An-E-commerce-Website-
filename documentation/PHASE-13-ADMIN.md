# Phase 13 — Admin Dashboard & CRUD

**Status:** Complete

## Admin Login

- URL: `http://localhost/shopq/public/admin/login`
- **Admin:** `admin@shopq.local` / `Admin@123`
- **Super Admin:** `superadmin@shopq.local` / `Admin@123`

## Modules

| Module | Path |
|--------|------|
| Dashboard | `/admin/dashboard` |
| Products | `/admin/products` |
| Categories | `/admin/categories` |
| Orders | `/admin/orders` |
| Customers | `/admin/customers` |
| Coupons | `/admin/coupons` |
| Banners | `/admin/banners` |
| Reviews | `/admin/reviews` |
| Settings | `/admin/settings` |

## What to Test

1. Admin login → sidebar navigation works
2. **Products:** add, edit, delete (soft)
3. **Categories:** create + edit
4. **Orders:** open order → change status to **Shipped**
5. **Coupons:** create a test coupon
6. **Banners:** add hero banner with image path
7. **Customers:** view customer + order list
8. **Settings:** update UPI ID / shipping rules
