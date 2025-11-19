'use client';

import { LayoutShell } from '../../../../components/layout-shell';
import { useQuery } from '@tanstack/react-query';
import api from '../../../../lib/api-client';

export default function MyDeliveriesPage() {
  const { data } = useQuery({
    queryKey: ['my-deliveries'],
    queryFn: async () => {
      const response = await api.get('/deliveries');
      return response.data;
    },
  });

  return (
    <LayoutShell role="FIELD_STAFF">
      <h1 className="text-2xl font-bold text-slate-900">My deliveries</h1>
      <p className="text-sm text-slate-500">Only the events captured with your secure token.</p>
      <div className="mt-6 overflow-hidden rounded-2xl border border-slate-100 bg-white shadow-sm">
        <table className="min-w-full divide-y divide-slate-100 text-sm">
          <thead className="bg-slate-50 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
            <tr>
              <th className="px-4 py-3">Date</th>
              <th className="px-4 py-3">School</th>
              <th className="px-4 py-3">Commodity</th>
              <th className="px-4 py-3 text-right">Quantity</th>
            </tr>
          </thead>
          <tbody className="divide-y divide-slate-100">
            {data?.data?.map((delivery: any) => (
              <tr key={delivery.id}>
                <td className="px-4 py-3">{delivery.delivery_date}</td>
                <td className="px-4 py-3">{delivery.school_name}</td>
                <td className="px-4 py-3">{delivery.commodity_name}</td>
                <td className="px-4 py-3 text-right font-semibold text-brand">{delivery.actual_quantity}</td>
              </tr>
            ))}
          </tbody>
        </table>
      </div>
    </LayoutShell>
  );
}
