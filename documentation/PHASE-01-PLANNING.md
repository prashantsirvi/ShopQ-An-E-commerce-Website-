# ShopQ — Phase 1: Planning Document

**Project:** ShopQ — Multi-Category E-Commerce Platform  
**Version:** 1.0.0-planning  
**Author:** ShopQ Engineering Team  
**Date:** July 23, 2026  
**Status:** Approved for Phase 2 (Architecture)

---

## 1. Executive Summary

ShopQ is a portfolio-grade, production-expandable e-commerce platform built with **Core PHP 8+**, **MySQL**, and **Vanilla JavaScript**. It combines proven UX patterns from modern marketplaces (product discovery, deals, wishlist, compare, checkout) without copying copyrighted UI.

**Dual objective:**
1. Satisfy college major-project requirements (documentation, modules, security, database design).
2. Provide a codebase that can evolve into a real business post-graduation.

---

## 2. Problem Statement

Small businesses and student developers need a **complete, understandable, secure** e-commerce reference—not a fragile tutorial app. ShopQ solves this by demonstrating enterprise patterns (MVC, PDO, RBAC, modular APIs) using a stack every XAMPP user can run locally.

---

## 3. Scope Definition

### 3.1 In Scope (MVP → Full Platform)

| Area | Features |
|------|----------|
| **Customer** | Home, categories, search, filters, product detail, cart, wishlist, compare, checkout, orders, profile, addresses, reviews |
| **Smart features** | Trending, recommendations, recently viewed, frequently bought together, popular searches (PHP logic, no ML) |
| **Payments** | Dummy card, Cash on Delivery, UPI QR structure; Razorpay-ready architecture |
| **Admin** | Dashboard, product/category/coupon/banner/order/customer management, analytics, settings |
| **Security** | PDO, CSRF, XSS prevention, password hashing, RBAC, secure uploads |
| **UX** | Responsive, skeleton loading, lazy images, dark-mode-ready CSS variables |

### 3.2 Out of Scope (Phase 1 — Future Versions)

- Native mobile apps (iOS/Android)
- Real Razorpay/Stripe live keys (architecture only in v1)
- Microservices / separate API gateway
- Redis, Elasticsearch, message queues
- Multi-vendor marketplace (single-store model in v1)
- Internationalization (i18n) — English only in v1
- Email/SMS providers (notification records + UI; actual SMTP optional later)

---

## 4. Stakeholders & User Roles

| Role | Description | Access |
|------|-------------|--------|
| **Guest** | Browse, search, view products | Public pages |
| **Customer** | Register, cart, checkout, orders, reviews | Authenticated customer area |
| **Admin** | Full back-office | `/admin/*` with RBAC |
| **Super Admin** | Settings, backup, system logs | Highest privilege |

---

## 5. Functional Requirements (FR)

### 5.1 Customer Module

- **FR-C01** Home page with hero banners, categories, deals, flash sale, best sellers, trending.
- **FR-C02** Category listing with pagination, filters (price, brand, rating, color), sorting.
- **FR-C03** Product detail: gallery, variants (color), stock, discount, related products, recently viewed.
- **FR-C04** Search with AJAX suggestions and popular searches.
- **FR-C05** Cart: add/update/remove, coupon apply, persist for logged-in users.
- **FR-C06** Wishlist and compare (session + DB for logged-in).
- **FR-C07** Checkout: address selection, payment method, order summary.
- **FR-C08** Order history, tracking status, invoice download (PDF/HTML).
- **FR-C09** Reviews and ratings (moderated by admin).
- **FR-C10** Profile, saved addresses, wallet balance display, membership tier.

### 5.2 Admin Module

- **FR-A01** Secure admin login separate from customer session namespace.
- **FR-A02** Dashboard with Chart.js analytics (sales, revenue, customers, top products).
- **FR-A03** CRUD for products (multi-image, variants, stock), categories, coupons, banners, offers.
- **FR-A04** Order management (status workflow: pending → confirmed → shipped → delivered → cancelled).
- **FR-A05** Customer management, review moderation, notification broadcast.
- **FR-A06** Database backup export, system log viewer, site settings.

### 5.3 Payment Module

- **FR-P01** Dummy payment (simulated success/failure for demo).
- **FR-P02** Cash on Delivery.
- **FR-P03** UPI QR display with transaction reference capture (manual verification flow).
- **FR-P04** Payment gateway abstraction interface for future Razorpay.

---

## 6. Non-Functional Requirements (NFR)

| ID | Requirement | Target |
|----|-------------|--------|
| NFR-01 | **Security** | OWASP top 10 mitigations; no raw SQL concatenation |
| NFR-02 | **Performance** | Page load < 3s on local XAMPP; indexed queries |
| NFR-03 | **Scalability** | Normalized DB; service-layer ready for extraction |
| NFR-04 | **Maintainability** | MVC separation; PSR-inspired naming |
| NFR-05 | **Usability** | Mobile-first responsive; WCAG-aware contrast |
| NFR-06 | **Reliability** | Transactional order creation; inventory checks |
| NFR-07 | **Portability** | Runs on XAMPP (Windows/Mac/Linux) |
| NFR-08 | **Testability** | Manual test checklists per phase; seed data |

---

## 7. Technology Decisions

| Layer | Choice | Rationale |
|-------|--------|-----------|
| Backend | PHP 8.1+ | Required stack; enums, readonly properties, match |
| Database | MySQL 8 / MariaDB | XAMPP default; proven for e-commerce |
| Frontend | HTML5 + Custom CSS + Vanilla JS | No framework lock-in; portfolio clarity |
| AJAX | Fetch API | Modern, promise-based |
| Charts | Chart.js | Lightweight admin analytics |
| Alerts | SweetAlert2 | Polished UX for confirmations |
| Icons | Font Awesome 6 | Consistent iconography |
| Server | Apache (XAMPP) | `.htaccess` URL rewriting |
| Auth | PHP sessions + password_hash() | Simple, secure, no JWT complexity for v1 |

---

## 8. Development Phases (Roadmap)

| Phase | Name | Deliverables |
|-------|------|--------------|
| **1** | Planning | This document, scope, requirements |
| **2** | Architecture | Folder structure, bootstrap, routing, autoload |
| **3** | Database | ERD, migrations SQL, seed data (~50 products) |
| **4** | Authentication | Register, login, logout, RBAC, CSRF |
| **5** | Frontend Layout | Header, footer, components, design system CSS |
| **6** | Product Module | Listing, detail, search, filters |
| **7** | Cart | Session + DB cart, AJAX updates |
| **8** | Wishlist | Add/remove, persistence |
| **9** | Compare | Side-by-side product comparison |
| **10** | Checkout | Address, summary, validation |
| **11** | Payments | Dummy, COD, UPI QR, gateway interface |
| **12** | Orders | Order creation, history, tracking, invoice |
| **13** | Admin Dashboard | CRUD modules, layout |
| **14** | Analytics | Chart.js dashboards, smart insights |
| **15** | Optimization | Caching headers, query tuning, lazy load |
| **16** | Testing | Full test plan, bug fixes, documentation |

**Rule:** One phase at a time. Each file delivered complete with mentor explanations.

---

## 9. High-Level System Architecture

```
┌─────────────────────────────────────────────────────────────┐
│                        Browser (Client)                      │
│   HTML Views │ Custom CSS │ Vanilla JS │ Chart.js │ AJAX    │
└──────────────────────────┬──────────────────────────────────┘
                           │ HTTP
┌──────────────────────────▼──────────────────────────────────┐
│                    Apache + .htaccess                        │
│              (Front Controller → public/index.php)           │
└──────────────────────────┬──────────────────────────────────┘
                           │
┌──────────────────────────▼──────────────────────────────────┐
│                      Application Layer                       │
│  Router → Middleware (Auth, CSRF, Admin) → Controllers       │
└──────────────────────────┬──────────────────────────────────┘
                           │
┌──────────────────────────▼──────────────────────────────────┐
│                       Business Layer                         │
│              Models │ Services │ Helpers │ Validators        │
└──────────────────────────┬──────────────────────────────────┘
                           │
┌──────────────────────────▼──────────────────────────────────┐
│                      Data Layer (PDO)                        │
│                    MySQL — shopq_database                    │
└─────────────────────────────────────────────────────────────┘
```

---

## 10. Database Planning (Preview for Phase 3)

### 10.1 Core Entity Groups

1. **Users & Auth** — `users`, `roles`, `user_roles`, `password_resets`
2. **Catalog** — `categories`, `products`, `product_images`, `product_variants`, `brands`
3. **Commerce** — `carts`, `cart_items`, `wishlists`, `compare_lists`, `coupons`, `orders`, `order_items`, `payments`
4. **Customer** — `addresses`, `reviews`, `notifications`, `wallets`, `wallet_transactions`, `memberships`
5. **Marketing** — `banners`, `offers`, `flash_sales`, `deals`
6. **Analytics** — `product_views`, `search_logs`, `order_analytics` (materialized via queries)
7. **System** — `settings`, `activity_logs`, `admin_sessions`

### 10.2 Design Principles

- **3NF normalization** with strategic denormalization only in `order_items` (price snapshot at purchase).
- **Soft deletes** on products (`deleted_at`) — never lose order history.
- **UUID or BIGINT** primary keys — we will use `BIGINT UNSIGNED AUTO_INCREMENT` for simplicity on XAMPP.
- **Indexes** on foreign keys, `slug`, `sku`, `email`, `status`, `created_at`.
- **~50 seed products** across 8–10 categories with 2–5 images each and color variants.

---

## 11. Security Planning

| Threat | Mitigation |
|--------|------------|
| SQL Injection | PDO prepared statements exclusively |
| XSS | `htmlspecialchars()` on output; CSP headers later |
| CSRF | Token per form; validate in middleware |
| Session hijacking | `session_regenerate_id()` on login; httponly cookies |
| Broken auth | `password_hash()` / `password_verify()`; rate limiting on login |
| File upload abuse | Whitelist MIME, rename files, store outside web root where possible |
| IDOR | Ownership checks on orders, addresses, reviews |
| Admin exposure | Separate `/admin` routes; role middleware |

---

## 12. URL Structure (Planned)

### Customer
- `/` — Home
- `/products` — Listing
- `/products/{slug}` — Detail
- `/category/{slug}` — Category
- `/search?q=` — Search
- `/cart`, `/wishlist`, `/compare`
- `/checkout`, `/orders`, `/profile`
- `/login`, `/register`, `/logout`

### Admin
- `/admin/login`
- `/admin/dashboard`
- `/admin/products`, `/admin/orders`, etc.

---

## 13. Design System (Preview for Phase 5)

| Token | Value | Usage |
|-------|-------|-------|
| Primary | `#2874F0` (inspired blue, not Flipkart copy) | CTAs, links |
| Accent | `#FF6B35` | Deals, flash sale |
| Success | `#2ECC71` | Stock, confirmations |
| Danger | `#E74C3C` | Errors, out of stock |
| Neutral | `#1A1A2E` / `#F8F9FA` | Text / backgrounds |
| Font | `Inter`, system-ui fallback | Body |
| Radius | `8px` cards, `12px` modals | Consistency |
| Dark mode | CSS custom properties | `[data-theme="dark"]` toggle ready |

---

## 14. Smart Features (PHP Logic)

| Feature | Algorithm (Simplified) |
|---------|------------------------|
| Trending | Products with highest views + orders in last 7 days |
| Best sellers | Top order quantity all-time / last 30 days |
| Recommended | Same category + overlapping tags + co-purchase |
| Frequently bought together | `order_items` co-occurrence count |
| Recently viewed | Session/DB `product_views` last 10 |
| Popular searches | `search_logs` GROUP BY query ORDER BY count |
| Customer segments | RFM-lite: recency of last order, frequency, monetary |

---

## 15. Risk Register

| Risk | Impact | Mitigation |
|------|--------|------------|
| Scope creep | High | Strict phase gate; MVP first |
| Inconsistent naming | Medium | Architecture doc in Phase 2 |
| SQL performance | Medium | Indexes, EXPLAIN in Phase 15 |
| Security gaps | High | Security checklist per phase |
| XAMPP path issues | Low | Document `BASE_URL` in `.env` |
| Large file uploads | Medium | Size limits in php.ini + validation |

---

## 16. Success Criteria

Phase 1 is complete when:
- [x] Scope, roles, and modules defined
- [x] NFRs and security plan documented
- [x] Phase roadmap locked (no skipping)
- [x] Database entity groups outlined
- [x] Tech stack confirmed
- [x] Project location: `C:\xampp\htdocs\shopq`

**Next:** Phase 2 — Architecture (folder structure, bootstrap, routing, autoload, `.htaccess`)

---

## 17. Glossary

| Term | Definition |
|------|------------|
| **MVC** | Model-View-Controller separation pattern |
| **RBAC** | Role-Based Access Control |
| **PDO** | PHP Data Objects — database abstraction |
| **CSRF** | Cross-Site Request Forgery |
| **Slug** | URL-friendly unique identifier (e.g. `wireless-earbuds-pro`) |
| **Variant** | Product attribute combination (e.g. color: Red) |
| **Gateway** | Payment provider abstraction layer |

---

*End of Phase 1 Planning Document*
