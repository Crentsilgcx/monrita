'use client';

import { useQuery } from '@tanstack/react-query';
import api from '../../../../lib/api-client';
import { LayoutShell } from '../../../../components/layout-shell';

export default function PlanPeriodsPage() {
  const { data } = useQuery({
    queryKey: ['plan-periods'],
    queryFn: async () => {
      const response = await api.get('/plan-periods');
      return response.data;
    },
  });

  return (
    <LayoutShell role="SUPER_ADMIN">
      <div className="flex items-center justify-between">
        <div>
          <h1 className="text-2xl font-bold text-slate-900">Plan periods</h1>
          <p className="text-sm text-slate-500">Manage academic years and terms.</p>
        </div>
        <button className="rounded-lg bg-brand px-4 py-2 text-sm font-semibold text-white">New period</button>
      </div>
      <div className="mt-6 overflow-hidden rounded-2xl border border-slate-100 bg-white shadow-sm">
        <table className="min-w-full divide-y divide-slate-100 text-sm">
          <thead className="bg-slate-50 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
            <tr>
              <th className="px-4 py-3">Label</th>
              <th className="px-4 py-3">Year</th>
              <th className="px-4 py-3">Term</th>
              <th className="px-4 py-3">Active</th>
            </tr>
          </thead>
          <tbody className="divide-y divide-slate-100">
            {data?.data?.map((period: any) => (
              <tr key={period.id}>
                <td className="px-4 py-3 font-medium text-slate-900">{period.label}</td>
                <td className="px-4 py-3">{period.academic_year}</td>
                <td className="px-4 py-3">{period.term}</td>
                <td className="px-4 py-3">
                  <span className={`rounded-full px-2 py-1 text-xs font-semibold ${period.is_active ? 'bg-brand/10 text-brand' : 'bg-slate-100 text-slate-500'}`}>
                    {period.is_active ? 'Active' : 'Inactive'}
                  </span>
                </td>
              </tr>
            ))}
          </tbody>
        </table>
      </div>
    </LayoutShell>
  );
}
