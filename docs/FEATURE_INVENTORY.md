# Experience & intelligence feature inventory (20 new highlights)

1. **Plan Metrics Panel** – `frontend/components/insights/plan-metrics-panel.tsx` renders denormalized totals with delivery density badges on super-admin pages.
2. **Metrics API endpoint** – `backend/src/Controllers/PlanPeriodsController.php::metrics` exposes `GET /api/v1/plan-periods/{id}/metrics` for any client.
3. **Plan-period metrics store** – `backend/database/migrations/001_create_tables.sql` introduces `plan_period_metrics` while `backend/src/Services/PlanPeriodMetricsService.php` keeps it in sync.
4. **Performance Playbook** – `frontend/components/insights/performance-playbook.tsx` highlights the 10 backend techniques so admins know the optimization levers.
5. **Feature Gallery matrix** – `frontend/components/insights/feature-gallery.tsx` showcases 20 polished UI touches for quick onboarding.
6. **Adaptive Ingestor Panel** – `frontend/components/import/adaptive-ingestor-panel.tsx` explains detected formats, batch size, and headers after each upload.
7. **Self-adaptive ingestion service** – `backend/src/Services/Ingestion/SelfAdaptivePlanIngestor.php` plus `XlsxStreamReader.php` support JSON/CSV/XLSX with synonym-aware headers.
8. **Detected header auditing** – `backend/src/Repositories/PlanImportRepository.php` persists `detected_headers` so ops can verify schema quality per file.
9. **Plan import summary UX** – `frontend/app/(dashboard)/super-admin/supply-plans/page.tsx` now shows ingestion results, plan metrics, and multi-format guidance.
10. **Axios ETag handshake** – `frontend/lib/api-client.ts` caches ETags per request key and replays them on GET calls to eliminate redundant payloads.
11. **Response-level ETags** – `backend/src/Utils/Response.php` hashes every JSON payload so browsers can short-circuit repeated dashboard calls.
12. **File-backed cache** – `backend/src/Services/Cache/FileCache.php` keeps hot summaries alive between requests with no external dependency.
13. **Prepared statement cache** – `backend/src/Services/Performance/StatementCache.php` reuses PDO statements for hot supply plan lookups.
14. **Adaptive chunk planning** – `SelfAdaptivePlanIngestor::determineChunkSize` scales batch size from 200–600 rows based on file size.
15. **Field knowledge explainers** – `frontend/app/(dashboard)/field-staff/page.tsx` now embeds `KnowledgeExplainers` to deliver inline definitions to agents.
16. **Delivery delta metrics sync** – `backend/src/Services/SupplyBalanceService.php` updates plan metrics inside the same transaction as each delivery.
17. **Plan metrics hook** – `frontend/hooks/use-plan-metrics.ts` standardizes TanStack Query consumption of the metrics endpoint.
18. **Plan metrics invalidation** – `frontend/app/(dashboard)/super-admin/supply-plans/page.tsx` invalidates `plan-metrics` queries after every import, keeping charts real-time.
19. **Feature-aware README guidance** – `README.md` documents the multi-format ingestion rules and the metrics API for downstream consumers.
20. **Performance documentation update** – `backend/docs/PERFORMANCE_FEATURES.md` now lists the 10 advanced techniques so auditors can trace each improvement to its code path.
