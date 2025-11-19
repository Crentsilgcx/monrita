'use client';

import { useMemo } from 'react';

type Props = {
  metrics?: {
    total_planned?: number;
    total_delivered?: number;
    total_remaining?: number;
    total_schools?: number;
    total_commodities?: number;
    plan_period_id?: number;
  } | null;
};

const metricTiles = [
  { key: 'total_planned', label: 'Total planned', accent: 'text-slate-900' },
  { key: 'total_delivered', label: 'Delivered to date', accent: 'text-emerald-600' },
  { key: 'total_remaining', label: 'Remaining balance', accent: 'text-brand' },
];

export function PlanMetricsPanel({ metrics }: Props) {
  const stats = useMemo(() => metrics ?? {}, [metrics]);
  const density = stats.total_planned ? Number(((stats.total_delivered ?? 0) / (stats.total_planned || 1)) * 100).toFixed(1) : '0.0';

  return (
    <section className="rounded-3xl border border-slate-100 bg-white p-6 shadow-sm">
      <div className="flex flex-col gap-2 md:flex-row md:items-center md:justify-between">
        <div>
          <p className="text-xs uppercase tracking-widest text-slate-400">Plan period metrics</p>
          <h2 className="text-2xl font-semibold text-slate-900">Operational snapshot</h2>
          <p className="text-sm text-slate-500">{density}% of planned stock has been delivered.</p>
        </div>
        <div className="rounded-full bg-brand/10 px-4 py-1 text-xs font-semibold text-brand">
          {stats.total_schools ?? 0} schools • {stats.total_commodities ?? 0} commodities
        </div>
      </div>
      <div className="mt-6 grid gap-4 md:grid-cols-3">
        {metricTiles.map((tile) => (
          <div key={tile.key} className="rounded-2xl border border-slate-100 bg-slate-50/50 p-4">
            <p className="text-xs uppercase tracking-wide text-slate-400">{tile.label}</p>
            <p className={`mt-2 text-3xl font-bold ${tile.accent}`}>
              {Number((stats as any)?.[tile.key] ?? 0).toLocaleString()}
            </p>
          </div>
        ))}
      </div>
    </section>
  );
}
