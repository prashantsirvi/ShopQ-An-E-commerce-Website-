# ShopQ Folder Structure

This explains where code actually lives. **Do not expect files in every top-level folder.**

## Empty folders at project root (ignore these)

These were created in Phase 2 as placeholders and were **never used**:

| Folder | Status | Why empty |
|--------|--------|-----------|
| `/admin` | Empty | Admin UI is in `views/admin/` + `controllers/Admin*.php` |
| `/api` | Empty | API routes are in `config/routes.php` (e.g. `/api/search/suggestions`) |
| `/assets` | Empty | Static files are in `public/assets/` |
| `/includes` | Empty | Replaced by `helpers/` + `bootstrap/` |

You can safely ignore or delete these empty root folders.

## Where things actually are

```
shopq/
├── public/                 ← Web root (Apache points here)
│   ├── index.php           ← Front controller
│   └── assets/
│       ├── css/app.css     ← All styles
│       ├── js/             ← app.js, commerce.js, products.js, admin.js
│       └── images/
├── controllers/            ← All PHP controllers (including Admin*)
├── models/                 ← Database models
├── views/
│   ├── admin/              ← Admin panel pages
│   ├── cart/, checkout/, orders/, payment/
│   ├── products/, layouts/, partials/
│   └── ...
├── config/routes.php       ← All URLs including /api/* and /admin/*
├── services/               ← Payment gateways
├── middleware/             ← Auth, admin guards
├── helpers/                ← auth.php, commerce.php, etc.
├── database/               ← Migrations + seeds
├── documentation/          ← Phase 1–16 docs
└── tests/                  ← Smoke tests
```

## Running the project

**XAMPP path (recommended for localhost):**
```
C:\xampp\htdocs\shopq
http://localhost/shopq/public
```

**Desktop copy (for VS Code editing):**
```
C:\Users\prash\OneDrive\Desktop\shopq
```
Keep Desktop in sync with `htdocs` after major changes, or open `C:\xampp\htdocs\shopq` directly in VS Code.

## Sync command (PowerShell)

Copy latest code from XAMPP to Desktop:

```powershell
robocopy "C:\xampp\htdocs\shopq" "C:\Users\prash\OneDrive\Desktop\shopq" /E /XD ".git" "storage\sessions" /XF "*.log"
```

Copy Desktop changes back to XAMPP (after you edit in VS Code):

```powershell
robocopy "C:\Users\prash\OneDrive\Desktop\shopq" "C:\xampp\htdocs\shopq" /E /XD ".git" "storage\sessions" /XF "*.log"
```
