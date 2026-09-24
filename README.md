# ShopQ

A modern multi-category e-commerce platform built with **Core PHP 8+**, **MySQL**, and **Vanilla JavaScript** — designed as a college major project and expandable into a production-ready shopping platform.

## Requirements

- [XAMPP](https://www.apachefriends.org/) (PHP 8.1+, MySQL, Apache)
- Modern web browser

## Local Setup (after Phase 3)

1. Clone or copy this project to `C:\xampp\htdocs\shopq`
2. Start Apache and MySQL in XAMPP Control Panel
3. Copy `.env.example` to `.env` and adjust credentials if needed
4. Run database setup:
   ```bash
   C:\xampp\php\php.exe database/setup.php
   ```
5. Open [http://localhost/shopq/public](http://localhost/shopq/public)
6. Health check: [http://localhost/shopq/public/health](http://localhost/shopq/public/health)

## Project Structure

See the [documentation index](documentation/README.md) for all 16 phase documents.

## Documentation

All docs are in `documentation/`:

- `PHASE-01-PLANNING.md` through `PHASE-16-TESTING.md`
- `TEST-PLAN.md` — full test checklist
- `README.md` — index with links

**Project path (XAMPP):** `C:\xampp\htdocs\shopq`

## Development Phases

| Phase | Status |
|-------|--------|
| 1 — Planning | Complete |
| 2 — Architecture | Complete |
| 3 — Database | Complete |
| 4 — Authentication | Complete |
| 5 — Frontend Layout | Complete |
| 6 — Product Module | Complete |
| 7 — Cart | Complete |
| 8 — Wishlist | Complete |
| 9 — Compare | Complete |
| 10 — Checkout | Complete |
| 11 — Payments | Complete |
| 12 — Orders | Complete |
| 13 — Admin Dashboard | Complete |
| 14 — Analytics | Complete |
| 15 — Optimization | Complete |
| 16 — Testing | Complete |

## Tech Stack

- **Backend:** PHP 8+ (MVC)
- **Database:** MySQL (PDO)
- **Frontend:** HTML5, Custom CSS, Vanilla JS, AJAX
- **Libraries:** Chart.js, Font Awesome, SweetAlert2

## License

Educational / portfolio use. Not affiliated with Flipkart, Amazon, or any trademarked brand.

## Documentation

All phase documents live in `/documentation`. See [documentation/README.md](documentation/README.md) for the full index.
