# Monrita Supply Allocation & Delivery Tracking Platform

A production-ready, API-first system for managing school supply plans and tracking deliveries from field staff. The project ships with a raw-PHP backend and a Next.js progressive web app frontend.

## Repository layout

```
backend/   # Pure PHP API (router, services, middleware, migrations)
frontend/  # Next.js App Router PWA with shadcn/ui patterns
```

## Backend setup (Windows friendly)

1. Install PHP 8.2+, Composer (optional), and MySQL 8 via WAMP/XAMPP.
2. Copy `backend/.env.example` to `.env` or export the variables in your web server environment.
3. Create the database defined in `DB_NAME` and run the migration SQL:
   ```bash
   mysql -u root -p monrita < backend/database/migrations/001_create_tables.sql
   ```
4. Seed demo users (creates a SUPER_ADMIN + FIELD_STAFF):
   ```bash
   php backend/database/seeds/seed.php
   ```
5. Configure Apache/Nginx to point `DocumentRoot` to `backend/public`.
6. Hit the API using curl/Postman. Example login:
   ```bash
   curl -X POST http://localhost/api/v1/auth/login \
     -H "Content-Type: application/json" \
     -d '{"email":"admin@example.com","password":"password"}'
   ```

### Key backend features

- Manual router (`backend/public/index.php`) and thin MVC structure under `src/`.
- PDO-only data access with prepared statements and transaction-safe delivery creation via `SupplyBalanceService`.
- Token-based auth + RBAC middleware (SUPER_ADMIN vs FIELD_STAFF).
- Reusable CRUD helpers for master data, and analytics endpoints for dashboards.
- SQL migrations covering all entities (schools, commodities, supply plans, deliveries, imports, metrics, tokens, etc.).
- Self-adaptive supply-plan imports via `POST /api/v1/supply-plan-imports` that auto-detect JSON/CSV/XLSX, chunk intelligently, and store detected headers for audits.
- Plan-period metrics service (`PlanPeriodMetricsService`) + `plan_period_metrics` table keep summary APIs constant-time and power the PWA Plan Metrics panel.

### Supply plan import formats

Upload JSON, CSV, or Excel files that contain `school_code`, `commodity_code`, and `planned_quantity` columns (the adaptive ingestor also understands synonyms such as `campus_code` or `qty`). Example JSON payload:

```json
[
  { "school_code": "SCH-001", "commodity_code": "RICE", "planned_quantity": 120.5 },
  { "school_code": "SCH-002", "commodity_code": "MAIZE", "planned_quantity": 90, "notes": "Boarding" }
]
```

CSV/Excel sheets simply need those headers in the first row. The PHP backend auto-detects the format, chunk-sizes the import (200–600 rows per batch depending on file size), writes detected headers to `plan_imports.detected_headers`, and enforces a 10 MB ceiling before any database work is attempted.

### Plan period metrics API

Every import and delivery writes into `plan_period_metrics`, so dashboards can fetch instant totals via `GET /api/v1/plan-periods/{id}/metrics`. The response contains `total_planned`, `total_delivered`, `total_remaining`, `total_schools`, and `total_commodities`, which the Next.js PWA renders inside the new Plan Metrics panel.

## Frontend setup (Next.js PWA)

1. Install Node.js 18+.
2. From `frontend/`, install deps and run the dev server:
   ```bash
   npm install
   npm run dev
   ```
3. Configure `NEXT_PUBLIC_API_BASE` in `.env.local` to point at the PHP API (default `http://localhost/api/v1`).
4. Visit `http://localhost:3000` – you can install the PWA thanks to `manifest.json` + `next-pwa` service worker.

### Frontend highlights

- App Router layout with role-specific dashboards, plan management, deliveries views, and field staff tools.
- shadcn/ui-inspired styling via Tailwind (green/white/gold palette) and Radix primitives.
- React Query-powered data hooks and optimistic UX for delivery capture.
- Offline-ready manifest + service worker for installability.

## Scaling & performance

- Database indexes on all heavy tables and pagination baked into every listing endpoint.
- Request-scoped caching and denormalized `remaining_quantity` values keep delivery writes fast.
- React Query caches, background refetching, and split components limit client re-renders.

## Testing quickstart

- Backend: hit `/api/v1/health` (coming soon) or existing endpoints via curl/Postman.
- Frontend: `npm run lint` and `npm run build` ensure the PWA compiles for production.

Enjoy building on top of Monrita!
