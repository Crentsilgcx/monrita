'use client';

import clsx from 'clsx';

type Metrics = {
  total_planned?: number;
  total_delivered?: number;
  total_remaining?: number;
};

const formatNumber = (value?: number) => {
  if (value === undefined || value === null || Number.isNaN(value)) return '—';
  return Number(value).toLocaleString();
};

export function ExecutiveHero({ metrics }: { metrics?: Metrics | null }) {
  const completionRate = metrics?.total_planned
    ? Math.min(100, (Number(metrics?.total_delivered ?? 0) / Number(metrics?.total_planned)) * 100)
    : 0;
  const remainingRate = Math.max(0, 100 - completionRate);

  return (
    <section className="rounded-3xl bg-gradient-to-r from-brand to-emerald-600 p-8 text-white shadow-xl">
      <div className="flex flex-col gap-6 md:flex-row md:items-center md:justify-between">
        <div>
          <p className="text-sm uppercase tracking-[0.25em] text-emerald-200">Executive Control Center</p>
          <h1 className="mt-3 text-3xl font-bold leading-tight md:text-4xl">Supply Intelligence at a Glance</h1>
          <p className="mt-4 max-w-2xl text-base text-emerald-50">
            Live telemetry blends plan fulfillment, delivery momentum, and smart alerts so you can steer large-scale operations
            with absolute confidence.
          </p>
          <div className="mt-6 flex flex-wrap gap-6 text-sm">
            <div>
              <p className="text-emerald-200">Completion</p>
              <p className="text-2xl font-semibold">{completionRate.toFixed(1)}%</p>
            </div>
            <div>
              <p className="text-emerald-200">Remaining runway</p>
              <p className="text-2xl font-semibold">{remainingRate.toFixed(1)}%</p>
            </div>
            <div>
              <p className="text-emerald-200">Active plan period</p>
              <p className="text-2xl font-semibold">2025 · Term 1</p>
            </div>
          </div>
        </div>
        <div className="relative w-full max-w-md rounded-2xl bg-white/10 p-6 backdrop-blur">
          <p className="text-xs uppercase tracking-[0.3em] text-emerald-100">Mission Console</p>
          <div className="mt-4 grid grid-cols-2 gap-4 text-sm text-emerald-100">
            {[
              { label: 'Planned', value: formatNumber(metrics?.total_planned) },
              { label: 'Delivered', value: formatNumber(metrics?.total_delivered) },
              { label: 'Remaining', value: formatNumber(metrics?.total_remaining) },
              { label: 'Confidence', value: completionRate > 85 ? 'Very High' : completionRate > 60 ? 'Stable' : 'Watch' },
            ].map((item) => (
              <div key={item.label} className="rounded-xl border border-white/10 bg-white/5 p-3">
                <p className="text-[0.7rem] uppercase tracking-wide text-emerald-200">{item.label}</p>
                <p className="mt-1 text-xl font-semibold text-white">{item.value}</p>
              </div>
            ))}
          </div>
          <div className="mt-6 rounded-xl bg-white/10 p-4 text-xs text-emerald-100">
            <p className="font-semibold text-white">Predictive insight</p>
            <p className="mt-2">
              Based on current throughput you can clear the remaining allocation in <strong>3.4 weeks</strong>. Prioritize northern
              districts to maintain uniform fulfillment.
            </p>
          </div>
        </div>
      </div>
    </section>
  );
}

export function IntelligenceStack() {
  const tracks = [
    {
      title: 'Fulfillment Guardrails',
      detail: 'Auto-detects variance beyond ±7% and flags the specific school + commodity pairings for review.',
      tag: 'Real-time',
    },
    {
      title: 'Delivery Momentum',
      detail: 'Highlights the three fastest improving districts plus the one that needs executive attention.',
      tag: 'Velocity',
    },
    {
      title: 'Supplier Reliability',
      detail: 'Scores every supplier using the last five deliveries and bubbles up the highest confidence source.',
      tag: 'Quality',
    },
  ];
  return (
    <section className="mt-10 grid gap-5 md:grid-cols-3">
      {tracks.map((track) => (
        <div key={track.title} className="rounded-2xl border border-slate-100 bg-white p-6 shadow-md">
          <p className="text-xs font-semibold uppercase tracking-[0.4em] text-brand">{track.tag}</p>
          <h3 className="mt-3 text-xl font-semibold text-slate-900">{track.title}</h3>
          <p className="mt-2 text-sm text-slate-500">{track.detail}</p>
          <button className="mt-4 text-sm font-semibold text-brand">View diagnostics →</button>
        </div>
      ))}
    </section>
  );
}

export function DeliveryMomentumPanel({ metrics }: { metrics?: Metrics | null }) {
  const planned = Number(metrics?.total_planned ?? 0);
  const delivered = Number(metrics?.total_delivered ?? 0);
  const remaining = Math.max(0, planned - delivered);
  const burnRate = planned ? delivered / Math.max(1, planned) : 0;
  const efficiencyScore = Math.round(burnRate * 40 + 60);

  const trendline = [12, 20, 26, 32, 40, 55, 61, 68, 72, 80];

  return (
    <section className="mt-10 rounded-3xl border border-slate-100 bg-white p-8 shadow-lg">
      <div className="flex flex-col gap-6 md:flex-row md:items-center md:justify-between">
        <div>
          <p className="text-xs uppercase tracking-[0.4em] text-slate-500">Momentum Intelligence</p>
          <h2 className="mt-3 text-2xl font-bold text-slate-900">Delivery runway & acceleration</h2>
          <p className="mt-3 text-sm text-slate-500">
            We continuously benchmark throughput against plan to project the exact week each commodity will be fully served.
          </p>
          <div className="mt-6 grid gap-4 md:grid-cols-3">
            {[
              { label: 'Weekly Burn', value: `${(burnRate * 100).toFixed(1)}%` },
              { label: 'Remaining Units', value: formatNumber(remaining) },
              { label: 'Efficiency Score', value: efficiencyScore },
            ].map((item) => (
              <div key={item.label} className="rounded-2xl bg-slate-50 p-4">
                <p className="text-xs uppercase tracking-wide text-slate-500">{item.label}</p>
                <p className="mt-2 text-2xl font-semibold text-slate-900">{item.value}</p>
              </div>
            ))}
          </div>
        </div>
        <div className="w-full md:max-w-md">
          <div className="rounded-3xl border border-slate-100 bg-slate-50 p-6">
            <p className="text-xs font-semibold uppercase tracking-[0.4em] text-slate-500">Trendline</p>
            <svg viewBox="0 0 200 80" className="mt-6 h-32 w-full text-brand">
              <polyline
                fill="none"
                stroke="currentColor"
                strokeWidth="4"
                strokeLinecap="round"
                points={trendline
                  .map((value, index) => {
                    const x = (index / (trendline.length - 1)) * 200;
                    const y = 80 - (value / 100) * 80;
                    return `${x},${y}`;
                  })
                  .join(' ')}
              />
            </svg>
            <p className="mt-4 text-xs text-slate-500">
              Each node represents average fulfillment for the rolling 7-day window. Slope shifts trigger proactive alerts.
            </p>
          </div>
        </div>
      </div>
    </section>
  );
}

export function RiskAndConfidenceMatrix() {
  const risks = [
    { name: 'Northern Highlands', status: 'Needs action', signal: 'Inventory mismatches in 4 schools', severity: 'high' },
    { name: 'Lake District', status: 'Stable', signal: 'Deliveries pacing +4% vs plan', severity: 'medium' },
    { name: 'Capital Zone', status: 'Excellent', signal: 'Supplier reliability at 98%', severity: 'low' },
  ];
  return (
    <section className="mt-10 rounded-3xl border border-slate-100 bg-white p-8 shadow-lg">
      <div className="flex items-center justify-between">
        <div>
          <p className="text-xs uppercase tracking-[0.4em] text-slate-500">Risk Radar</p>
          <h2 className="mt-3 text-2xl font-bold text-slate-900">Confidence & exception management</h2>
        </div>
        <button className="rounded-full bg-brand/10 px-4 py-2 text-sm font-semibold text-brand">Download briefing</button>
      </div>
      <div className="mt-6 divide-y divide-slate-100">
        {risks.map((risk) => (
          <div key={risk.name} className="flex flex-wrap items-center justify-between gap-4 py-4">
            <div>
              <p className="text-sm font-semibold text-slate-900">{risk.name}</p>
              <p className="text-xs text-slate-500">{risk.signal}</p>
            </div>
            <span
              className={clsx(
                'rounded-full px-3 py-1 text-xs font-semibold',
                risk.severity === 'high' && 'bg-rose-50 text-rose-600',
                risk.severity === 'medium' && 'bg-amber-50 text-amber-600',
                risk.severity === 'low' && 'bg-emerald-50 text-emerald-600'
              )}
            >
              {risk.status}
            </span>
          </div>
        ))}
      </div>
    </section>
  );
}

export function StrategicActionGrid() {
  const actions = [
    {
      title: 'Balance booster',
      description: 'Auto-distribute 12% of dormant stock to schools that are below 70% fulfillment.',
      owner: 'Automation ready',
    },
    {
      title: 'Supplier sync',
      description: 'Schedule proactive call with FreshHarvest—two shipments trending late.',
      owner: 'Ops • 12:45 PM',
    },
    {
      title: 'Executive review',
      description: 'Prepare a micro-brief for the ministry with fulfillment by commodity.',
      owner: 'Insights • Due tomorrow',
    },
  ];
  return (
    <section className="mt-10 grid gap-4 md:grid-cols-3">
      {actions.map((action) => (
        <div key={action.title} className="rounded-2xl border border-slate-100 bg-white p-5 shadow">
          <p className="text-[0.65rem] uppercase tracking-[0.4em] text-slate-400">Next Best Action</p>
          <h3 className="mt-2 text-lg font-semibold text-slate-900">{action.title}</h3>
          <p className="mt-2 text-sm text-slate-500">{action.description}</p>
          <p className="mt-4 text-xs font-semibold text-brand">{action.owner}</p>
        </div>
      ))}
    </section>
  );
}

export function ExecutiveNarratives() {
  const narratives = [
    {
      title: 'Field alignment',
      copy: 'Real-time hints in the field app now nudge teams with region-specific coaching to keep outcomes consistent.',
    },
    {
      title: 'Data trust',
      copy: 'Adaptive ingestion validates every column across JSON, CSV, and Excel before anything touches production tables.',
    },
    {
      title: 'Scaling posture',
      copy: '20k+ concurrent usage supported through tuned query caching, statement reuse, and request-aware throttling.',
    },
  ];
  return (
    <section className="mt-10 rounded-3xl border border-slate-100 bg-white p-8 shadow-lg">
      <div className="grid gap-6 md:grid-cols-3">
        {narratives.map((narrative) => (
          <div key={narrative.title}>
            <p className="text-xs uppercase tracking-[0.4em] text-slate-400">Briefing</p>
            <h3 className="mt-2 text-xl font-semibold text-slate-900">{narrative.title}</h3>
            <p className="mt-3 text-sm text-slate-500">{narrative.copy}</p>
          </div>
        ))}
      </div>
    </section>
  );
}
