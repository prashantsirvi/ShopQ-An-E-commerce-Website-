# ShopQ — Full Test Plan

**Version:** 1.0.0  
**Date:** July 23, 2026

## Environment

- XAMPP Apache + MySQL running
- Project at `C:\xampp\htdocs\shopq`
- Database seeded via `database/setup.php`

## Automated Smoke Tests

```powershell
C:\xampp\php\php.exe C:\xampp\htdocs\shopq\tests\run-all.php
```

## Test Accounts

| Role | Email | Password |
|------|-------|----------|
| Customer | customer@shopq.local | Admin@123 |
| Admin | admin@shopq.local | Admin@123 |
| Super Admin | superadmin@shopq.local | Admin@123 |

## Phase-by-Phase Verification

See `PHASE-01-PLANNING.md` through `PHASE-16-TESTING.md` for detailed test steps per phase.

## Critical Paths

1. **Guest shopping** → cart → login merge → checkout
2. **Online payment** → dummy success/failure
3. **Admin order fulfillment** → status shipped → customer sees timeline
4. **Analytics** → dashboard reflects new orders

## Known Limitations (v1)

- Razorpay stub only (keys in `.env` for future wiring)
- Banner images use path strings (no upload UI)
- Review submission UI not on product page (admin moderation ready)
- Email notifications not sent (DB records only)
