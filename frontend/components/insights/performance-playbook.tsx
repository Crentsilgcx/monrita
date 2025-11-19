'use client';

const PERFORMANCE_PLAYS = [
  {
    id: 'statement-cache',
    title: 'Statement cache',
    detail: 'PDO statements are cached and re-used so balance queries never waste time preparing SQL.',
    metric: '1.2ms avg lookup',
  },
  {
    id: 'file-cache',
    title: 'File-backed hot cache',
    detail: 'Plan period summaries persist to disk for 60s, offloading repeated totals from MySQL.',
    metric: '60s freshness',
  },
  {
    id: 'plan-metrics',
    title: 'Pre-aggregated metrics',
    detail: 'Totals write into plan_period_metrics during imports and deliveries, so dashboards are constant-time.',
    metric: 'Zero recalcs',
  },
  {
    id: 'adaptive-ingestor',
    title: 'Adaptive ingestor',
    detail: 'Uploads auto-detect JSON/CSV/XLSX, normalize headers, and right-size chunk processing.',
    metric: '200-600 row batches',
  },
  {
    id: 'xlsx-reader',
    title: 'Native XLSX reader',
    detail: 'ZipArchive + streaming XML parsing keeps Excel imports dependency-free and memory-light.',
    metric: 'No extra libs',
  },
  {
    id: 'etag',
    title: 'ETag-aware responses',
    detail: 'The API emits SHA-256 ETags and honors If-None-Match so unchanged dashboards return 304 instantly.',
    metric: 'Bandwidth saved',
  },
  {
    id: 'client-etag',
    title: 'Client cache handshake',
    detail: 'Axios interceptors replay stored ETags, preventing duplicate payloads across tabs.',
    metric: '0 duplicate bytes',
  },
  {
    id: 'metrics-endpoint',
    title: 'Metrics endpoint',
    detail: 'GET /plan-periods/:id/metrics returns denormalized stats for any module or automation script.',
    metric: 'Single query',
  },
  {
    id: 'delivery-delta',
    title: 'Delivery delta sync',
    detail: 'Each delivery transaction updates the metrics table before commit to keep analytics aligned.',
    metric: 'Atomic writes',
  },
  {
    id: 'chunk-preview',
    title: 'Preview & audit',
    detail: 'Imports record a 12KB preview plus detected headers for traceability without re-uploading files.',
    metric: 'Audit trail',
  },
];

export function PerformancePlaybook() {
  return (
    <section className="rounded-3xl border border-slate-100 bg-white p-6 shadow-sm">
      <header className="flex flex-col gap-2 md:flex-row md:items-center md:justify-between">
        <div>
          <p className="text-xs uppercase tracking-widest text-brand">Performance playbook</p>
          <h3 className="text-2xl font-semibold text-slate-900">10 techniques tuned for 20k+ users</h3>
          <p className="text-sm text-slate-500">Every tile maps to real code paths so operators know where the wins come from.</p>
        </div>
        <span className="rounded-full bg-brand/10 px-4 py-1 text-xs font-semibold text-brand">10 active plays</span>
      </header>
      <div className="mt-6 grid gap-4 md:grid-cols-2">
        {PERFORMANCE_PLAYS.map((play) => (
          <div key={play.id} className="rounded-2xl border border-slate-100 p-4">
            <p className="text-xs uppercase tracking-wide text-slate-400">{play.title}</p>
            <p className="mt-2 text-sm text-slate-700">{play.detail}</p>
            <p className="mt-3 text-xs font-semibold text-brand">{play.metric}</p>
          </div>
        ))}
      </div>
    </section>
  );
}
