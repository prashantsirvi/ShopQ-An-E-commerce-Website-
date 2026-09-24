# Phase 2 — Architecture

**Status:** Complete  
**Date:** July 23, 2026

## Overview

Phase 2 establishes the application skeleton: front controller, routing, MVC layers, middleware pipeline, environment loading, and secure session bootstrap.

## Folder Structure

```
shopq/
├── bootstrap/
│   ├── app.php              # App bootstrap + constants
│   └── autoload.php         # PSR-4 style autoloader + helpers
├── config/
│   ├── app.php              # Application settings
│   ├── database.php         # PDO connection settings
│   └── routes.php           # Route definitions
├── core/
│   ├── App.php              # Application kernel
│   ├── Router.php           # HTTP router + middleware
│   ├── Controller.php       # Base controller
│   ├── Model.php            # Base model
│   ├── Database.php         # PDO singleton
│   ├── Request.php          # HTTP request wrapper
│   ├── Response.php         # Redirect/JSON helpers
│   └── View.php             # View renderer
├── controllers/
│   └── HomeController.php   # Sample route controller
├── middleware/
│   ├── MiddlewareInterface.php
│   ├── AuthMiddleware.php   # Stub — Phase 4
│   └── AdminMiddleware.php  # Stub — Phase 4/13
├── helpers/
│   ├── env.php
│   ├── session.php
│   ├── csrf.php
│   ├── sanitize.php
│   └── url.php
├── views/
│   ├── layouts/main.php
│   ├── home/index.php
│   └── errors/404.php, 500.php
├── public/
│   ├── index.php            # Front controller
│   ├── .htaccess
│   └── assets/css, js
├── database/                # Phase 3
├── uploads/                 # Phase 6+
├── logs/
├── storage/
└── documentation/
```

## Request Lifecycle

1. Browser hits `/shopq/public/` or any route
2. Apache `mod_rewrite` sends request to `public/index.php`
3. Bootstrap loads `.env`, helpers, autoloader, session
4. `Core\App` loads `config/routes.php`
5. `Router` matches URI + HTTP method
6. Middleware pipeline runs (if attached)
7. Controller action executes
8. View renders with optional layout

## Key Design Decisions

| Decision | Why |
|----------|-----|
| Front controller in `/public` | Keeps PHP source outside web root where possible |
| Singleton PDO | One connection per request; prepared statements everywhere |
| Middleware pipeline | Auth/admin guards reusable across routes |
| `.env` configuration | No hardcoded credentials; XAMPP-friendly |
| Session files in `/storage/sessions` | Writable path separate from codebase |
| CSRF helpers early | Forms in Phase 4+ inherit protection by default |

## Routes (Phase 2)

| Method | URI | Handler |
|--------|-----|---------|
| GET | `/` | HomeController@index |
| GET | `/health` | JSON health check |

## Testing

1. Start Apache in XAMPP
2. Visit `http://localhost/shopq/public/`
3. Visit `http://localhost/shopq/public/health` — expect JSON
4. Visit `http://localhost/shopq/public/unknown` — expect 404 page

## Next Phase

**Phase 3 — Database:** ERD, SQL migrations, seed data (~50 products).
