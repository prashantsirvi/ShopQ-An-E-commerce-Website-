# Phase 4 — Authentication

**Status:** Complete  
**Date:** July 23, 2026

## Features Delivered

| Feature | Implementation |
|---------|----------------|
| Customer register | `/register` with validation + wallet creation |
| Customer login | `/login` with CSRF + rate limiting |
| Customer logout | POST `/logout` with CSRF |
| Admin login | `/admin/login` — separate session namespace |
| Admin logout | POST `/admin/logout` |
| RBAC | Role slugs: `customer`, `admin`, `super_admin` |
| Protected routes | `AuthMiddleware`, `AdminMiddleware` |
| Guest routes | `GuestMiddleware`, `AdminGuestMiddleware` |
| Session security | `session_regenerate_id()` on login/logout |
| Password security | `password_hash()` / `password_verify()` |
| CSRF | All auth forms protected |
| Intended URL redirect | Returns user to protected page after login |

## Session Structure

```php
$_SESSION['customer'] = ['id', 'name', 'email', 'role_id', 'role_slug'];
$_SESSION['admin']    = ['id', 'name', 'email', 'role_id', 'role_slug'];
```

Customer and admin sessions are isolated by design.

## Routes

| Method | URI | Access |
|--------|-----|--------|
| GET | `/login` | Guest |
| POST | `/login` | Guest |
| GET | `/register` | Guest |
| POST | `/register` | Guest |
| POST | `/logout` | Customer |
| GET | `/profile` | Customer |
| GET | `/admin/login` | Admin guest |
| POST | `/admin/login` | Admin guest |
| POST | `/admin/logout` | Admin |
| GET | `/admin/dashboard` | Admin |

## Test Accounts

| Type | Email | Password |
|------|-------|----------|
| Customer | customer@shopq.local | Admin@123 |
| Admin | admin@shopq.local | Admin@123 |
| Super Admin | superadmin@shopq.local | Admin@123 |

## Next Phase

**Phase 5 — Frontend Layout:** Design system, reusable components, header/footer enhancements, dark mode toggle.
