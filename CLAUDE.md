# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

Desktop quotation generator for **Smart Rental (LINEAR CHANNEL SDN BHD)** Flexi Rental service. Generates PDF quotations for laptop/iPad rentals.

**Structure:**
```
flexi-rental/
├── laravel/      # PHP backend (Laravel 13.13, SQLite, Blade + Alpine.js)
└── electron/     # Desktop wrapper (Electron 35, port 8765)
```

## Development Commands

All Laravel commands run from `laravel/`:

```bash
# Start dev server (required before using the app)
php artisan serve --port=8765

# Run migrations fresh + reseed plans
php artisan migrate:fresh --seed

# Run only new migrations
php artisan migrate

# Tinker / DB inspection
php artisan tinker --execute="echo json_encode(App\Models\Quotation::latest()->first()->toArray());"
```

Run Electron (from `electron/`):
```bash
npx electron .         # dev — spawns PHP server automatically
npm run build          # package to Windows NSIS installer
```

## Architecture

### Request Flow
Browser (Electron or dev) → `routes/web.php` → Controller → Blade view (with Alpine.js reactivity)

Financial calculations happen **twice**: once in Alpine.js (live preview) and again server-side in the controller (source of truth stored in DB). Both must stay in sync.

### Calculation Logic (critical — must match in both Alpine and PHP)
```
total_days  = (end_date - start_date).days + 1   // both ends inclusive
rental_fee  = rate_per_day × quantity × total_days
subtotal    = rental_fee + delivery_fee           // taxable base (NO deposit)
tax_amount  = subtotal × (tax_percent / 100)      // tax on rental + delivery ONLY
total_payable = subtotal + tax_amount + deposit   // deposit is NOT taxed (no SST)
```

**Carbon 3 gotcha:** `$end->diffInDays($start)` returns **negative** in Carbon 3 (Laravel 13). Always use `$start->diffInDays($end)`.

**Date parsing in JS:** Always parse dates as local time with `new Date(year, month-1, day)` — never `new Date('YYYY-MM-DD')` which is UTC and causes off-by-one errors.

### Database (SQLite at `laravel/database/database.sqlite`)

Key tables:
- `plans` — rental plans with rates; seeded from `PlanSeeder`. `is_custom=true` means user enters rate/name manually on quotation form.
- `customers` — reusable customer records; nullable FK on quotations.
- `quotations` — stores all computed fields (rental_fee, subtotal, tax_amount, total_payable) denormalized for PDF rendering. `quotation_no` format: `FLEXI-10001` auto-incremented via `Quotation::generateNumber()`.

`subtotal` column = `rental_fee + delivery_fee` (pre-tax taxable base), NOT the grand total.

### View Architecture

`quotations/create.blade.php` and `quotations/edit.blade.php` each contain two Alpine components defined as `<script>` functions:
- `customerPicker(customers)` — handles customer dropdown + quick-create modal (saves via `fetch('/customers')` JSON POST)
- `quotationForm(plans[, existing])` — handles plan selection, date calculation, live financial summary

`quotations/_form.blade.php` is shared between create and edit. It uses `x-model` bindings and `$watch` (not `@change`) for reactive date calculations.

### PDF Generation
`barryvdh/laravel-dompdf` via `QuotationController::pdf()`. Template: `quotations/pdf.blade.php`.
- `enable_php = true` in `config/dompdf.php` — required for `<script type="text/php">` page number canvas script
- Logo uses `public_path('images/logo.svg')` (absolute path required by DomPDF, not `asset()`)
- Page break: `page-break-after: always` on `.page-1` div (page 1), not page 2

### Customer Quick-Create Modal
In quotation form, clicking "+ New" opens an Alpine modal that POSTs to `/customers` with `Accept: application/json`. `CustomerController::store()` returns JSON when `$request->wantsJson()`. On success, new customer is pushed into the Alpine `customers` array and auto-selected — no page reload.

### Electron Integration
`electron/main.js` spawns `php artisan serve --port=8765` as a child process, polls the port until ready, then opens a `BrowserWindow` pointing to `http://127.0.0.1:8765`. On app quit, kills the PHP process. When packaged, Laravel files land in `resources/laravel/` via `extraResources` in `electron/package.json`.
