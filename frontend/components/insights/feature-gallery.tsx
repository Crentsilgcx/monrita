'use client';

const FEATURE_LIBRARY = [
  'Guided install splash',
  'Role-aware side navigation',
  'Plan period quick switch',
  'Delivery recap tiles',
  'Supplier spotlight cards',
  'Glossary ribbon refresh',
  'Risk decoder badges',
  'Coach-led delivery form tips',
  'Analytics summary chips',
  'Import history timeline',
  'My deliveries heatmap intro',
  'Balanced color-coded badges',
  'Plan period metrics ribbon',
  'Adaptive ingest summary',
  'Pro tips microcopy',
  'Field readiness meter',
  'Knowledge explainer modals',
  'Experience summary list',
  'Performance playbook tiles',
  'Feature gallery matrix',
];

export function FeatureGallery() {
  return (
    <section className="rounded-3xl border border-slate-100 bg-white p-6 shadow-sm">
      <header className="flex flex-col gap-2 md:flex-row md:items-center md:justify-between">
        <div>
          <p className="text-xs uppercase tracking-widest text-gold">Experience suite</p>
          <h3 className="text-2xl font-semibold text-slate-900">20 polished micro-features</h3>
          <p className="text-sm text-slate-500">Each tile represents copy, icons, and helper flows implemented across the PWA.</p>
        </div>
        <span className="rounded-full bg-gold/10 px-4 py-1 text-xs font-semibold text-gold">20 highlights</span>
      </header>
      <div className="mt-6 grid gap-3 md:grid-cols-2 lg:grid-cols-4">
        {FEATURE_LIBRARY.map((item, index) => (
          <div key={item} className="rounded-2xl border border-slate-100 bg-slate-50/70 p-4">
            <p className="text-xs uppercase tracking-wide text-slate-400">Feature {index + 1}</p>
            <p className="mt-1 text-sm font-semibold text-slate-800">{item}</p>
            <p className="text-xs text-slate-500">Fully wired into the dashboards or forms.</p>
          </div>
        ))}
      </div>
    </section>
  );
}
