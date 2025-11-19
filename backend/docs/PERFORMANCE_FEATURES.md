# Custom performance features (no Redis required)

## Advanced performance pack (new top 10 techniques)

1. **Prepared statement cache** – `src/Services/Performance/StatementCache.php` keeps PDO statements warm for balance lookups and row-lock queries.
2. **File-backed hot cache** – `src/Services/Cache/FileCache.php` stores serialized payloads on disk so expensive totals only hit MySQL once per minute.
3. **Plan period metrics table** – `plan_period_metrics` (see `database/migrations/001_create_tables.sql`) stores denormalized totals so dashboards skip aggregate scans.
4. **Delivery delta sync** – `src/Services/SupplyBalanceService.php` now calls `PlanPeriodMetricsService::applyDeliveryDelta` inside the transaction to keep analytics aligned with each write.
5. **Self-adaptive ingestor** – `src/Services/Ingestion/SelfAdaptivePlanIngestor.php` auto-detects JSON/CSV/XLSX, maps synonym headers, and right-sizes chunking (200–600 rows per batch).
6. **Native XLSX streaming** – `src/Services/Ingestion/XlsxStreamReader.php` uses ZipArchive + XML parsing to read Excel sheets without third-party packages.
7. **Header+preview auditing** – `PlanImportService` records a 12 KB preview and the detected headers into `plan_imports.detected_headers` for forensics.
8. **ETag & conditional GET** – `src/Utils/Response.php` emits SHA-256 ETags and returns 304s when `If-None-Match` matches, slashing network chatter.
9. **Client-side ETag reuse** – `frontend/lib/api-client.ts` replays stored ETags in Axios interceptors so the browser never re-downloads identical payloads.
10. **Metrics API endpoint** – `GET /api/v1/plan-periods/{id}/metrics` (handled by `PlanPeriodsController::metrics`) serves constant-time stats consumed by the PWA Plan Metrics panel.

1. **Persistent PDO connections & tuned options** – `backend/src/Config/Database.php` pins persistent handles, disables emulated prepares, enables buffered queries, and tightens timeouts so every request reuses a warm connection without extra dependencies.
2. **Strict session defaults** – the DB bootstrap flips `innodb_strict_mode` on immediately, preventing slow table scans caused by lax SQL semantics.
3. **Request-scoped cache utility** – `backend/src/Services/Cache/RequestCache.php` gives controllers and repositories a zero-dependency memoization layer for hot lookups.
4. **Cached master-data pagination** – `backend/src/Repositories/CrudRepository.php` layers the request cache on top of pagination so repeated school/commodity fetches hit memory, not MySQL.
5. **Pagination guardrail** – `backend/src/Utils/Pagination.php` clamps page sizes everywhere (`DeliveriesController`, `SupplyPlansController`, `PlanPeriodsController`, `MasterDataController`) to keep unbounded scans out of the query plan.
6. **Response timing & HTTP cache hints** – `backend/src/Utils/Response.php` stamps every JSON response with `X-Response-Time` plus smart `Cache-Control` headers to help upstream CDNs reuse GET payloads safely.
7. **Lookup dictionaries by code** – `backend/src/Repositories/LookupRepository.php` preloads schools/commodities once per request so imports and balances avoid thousands of point queries.
8. **Plan balance memoization** – `backend/src/Repositories/SupplyPlanRepository.php` caches `findBalance` results and invalidates them from `SupplyBalanceService` after each delivery, keeping balance reads constant time.
9. **Row-level locking for deliveries** – `backend/src/Services/SupplyBalanceService.php` promotes the supply plan read to `SELECT … FOR UPDATE`, eliminating race conditions and rework when 20k users hit the same plan row.
10. **Bulk upserts with delta-aware remaining math** – `backend/src/Repositories/SupplyPlanRepository.php::bulkUpsert` writes hundreds of plan rows per statement and preserves delivered deltas so recalculations never walk the deliveries table.
11. **Chunked JSON imports** – `backend/src/Services/PlanImportService.php` slices uploads into 500-row blocks before writing, keeping memory usage flat even for huge districts.
12. **Deduplicated + size-capped imports** – the same service drops repeated `(school, commodity)` rows, enforces a 5 MB ceiling, and streams only a 10 KB preview into `plan_imports` for auditability.
13. **Import-friendly endpoints** – `backend/src/Controllers/SupplyPlanImportsController.php` and `backend/src/Repositories/PlanImportRepository.php` expose lightweight history APIs so the UI can poll once and reuse cached histories.
14. **Plan balance endpoint TTLs** – controllers such as `DeliveriesController::balance` now attach cache TTLs so frequently-read balances can live briefly in browser caches without hammering PHP.
15. **Optimized QueryClient defaults** – `frontend/lib/query-client.ts` widens `staleTime`, `gcTime`, and enables `keepPreviousData`, eliminating dozens of redundant re-fetches while keeping data fresh in the PWA.
16. **Axios request deduplication** – `frontend/lib/api-client.ts` maintains an in-memory map of inflight GETs (per URL/params) and cancels duplicates with `AbortController`, plus enforces a 10 s timeout for runaway calls.
17. **Plan summary memoization** – `frontend/app/(dashboard)/super-admin/supply-plans/page.tsx` uses `useMemo` to derive total planned/remaining values once per fetch instead of on every row render.
18. **React Query-powered import history** – the same page relies on the cache-aware `/supply-plan-imports` query so the dashboard doesn’t re-pull history while users interact with filters.
19. **Client-side pagination defaults** – supply plan queries request only the first 100 rows per page, matching the backend guardrail and preventing giant table renders.
20. **Upload path short-circuiting** – `PlanImportService` validates MIME type, file size, and school/commodity codes before DB work begins, shielding the database from malformed payloads early in the request lifecycle.
