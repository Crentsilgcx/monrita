'use client';

import { LayoutShell } from '../../../components/layout-shell';
import Link from 'next/link';
import { FieldExperienceCoach, KnowledgeExplainers } from '../../../components/experience/pro-experience';

export default function FieldStaffDashboard() {
  return (
    <LayoutShell role="FIELD_STAFF">
      <div className="flex items-center justify-between">
        <div>
          <h1 className="text-2xl font-bold text-slate-900">Hello, Field Lead</h1>
          <p className="text-sm text-slate-500">Your last sync was 4 minutes ago.</p>
        </div>
        <Link href="/(dashboard)/field-staff/new-delivery" className="rounded-lg bg-brand px-4 py-2 text-sm font-semibold text-white">
          Record delivery
        </Link>
      </div>
      <div className="mt-6 grid gap-6 md:grid-cols-3">
        {[
          { label: 'Deliveries this week', value: '12' },
          { label: 'Tonnage captured', value: '24.5 MT' },
          { label: 'Schools remaining', value: '3' },
        ].map((card) => (
          <div key={card.label} className="rounded-2xl border border-slate-100 bg-white p-6 shadow-sm">
            <p className="text-sm uppercase text-slate-500">{card.label}</p>
            <p className="text-3xl font-semibold text-brand">{card.value}</p>
          </div>
        ))}
      </div>
      <FieldExperienceCoach />
      <KnowledgeExplainers />
    </LayoutShell>
  );
}
