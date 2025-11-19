'use client';

import { LayoutShell } from '../../../../components/layout-shell';

const sections = [
  { title: 'Schools', description: 'Geo coded location intelligence with enrollment details' },
  { title: 'Commodities', description: 'Units, packaging, nutritional baseline' },
  { title: 'Suppliers', description: 'Vendor SLAs and contact networks' },
];

export default function MastersPage() {
  return (
    <LayoutShell role="SUPER_ADMIN">
      <h1 className="text-2xl font-bold text-slate-900">Master data</h1>
      <p className="text-sm text-slate-500">All engines feed the single source of truth for allocations.</p>
      <div className="mt-6 grid gap-6 md:grid-cols-3">
        {sections.map((section) => (
          <div key={section.title} className="rounded-2xl border border-slate-100 bg-white p-6 shadow-sm">
            <p className="text-lg font-semibold text-slate-900">{section.title}</p>
            <p className="text-sm text-slate-500">{section.description}</p>
            <button className="mt-4 rounded-lg border border-brand px-4 py-2 text-sm font-semibold text-brand">Manage</button>
          </div>
        ))}
      </div>
    </LayoutShell>
  );
}
