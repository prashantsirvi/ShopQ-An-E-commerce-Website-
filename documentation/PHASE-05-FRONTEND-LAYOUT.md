# Phase 5 — Frontend Layout

**Status:** Complete  
**Date:** July 23, 2026

## Deliverables

| Component | Location |
|-----------|----------|
| Design tokens | `public/assets/css/app.css` (`:root`, dark theme) |
| Header + topbar | `views/partials/header.php` |
| Footer | `views/partials/footer.php` |
| Search bar UI | `views/partials/search-bar.php` |
| Mobile drawer | `views/partials/mobile-drawer.php` |
| Product card | `views/partials/product-card.php` |
| Product skeleton | `views/partials/product-card-skeleton.php` |
| Category card | `views/partials/category-card.php` |
| Section heading | `views/partials/section-heading.php` |
| Home page | `views/home/index.php` (live DB data) |
| Theme + UX JS | `public/assets/js/app.js` |

## Home Page Sections

1. Hero banner with promo cards
2. Shop by category (10 categories from DB)
3. Flash sale + countdown timer
4. Featured products
5. Best sellers
6. Trending products
7. Newsletter CTA

## Reusable Components

All partials are rendered via `View::partial('partials/name', $data)` and can be reused in Phase 6 product pages.

## UX Features

- Dark mode toggle (persisted in `localStorage`)
- Mobile navigation drawer
- Lazy-loaded product images with fade-in
- Search suggestion dropdown (UI)
- Skeleton loading component
- Responsive grids (4 → 3 → 2 columns)

## Next Phase

**Phase 6 — Product Module:** Full catalog, filters, search, product detail pages.
