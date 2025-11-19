'use client';

import { LayoutShell } from '../../../../components/layout-shell';
import { useQuery } from '@tanstack/react-query';
import api from '../../../../lib/api-client';

export default function UsersPage() {
  const { data } = useQuery({
    queryKey: ['users'],
    queryFn: async () => {
      const response = await api.get('/users');
      return response.data;
    },
  });

  return (
    <LayoutShell role="SUPER_ADMIN">
      <div className="flex items-center justify-between">
        <div>
          <h1 className="text-2xl font-bold text-slate-900">User management</h1>
          <p className="text-sm text-slate-500">Rapidly onboard field staff at national scale.</p>
        </div>
        <button className="rounded-lg bg-gold px-4 py-2 text-sm font-semibold text-white">Invite user</button>
      </div>
      <div className="mt-6 overflow-hidden rounded-2xl border border-slate-100 bg-white shadow-sm">
        <table className="min-w-full divide-y divide-slate-100 text-sm">
          <thead className="bg-slate-50 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
            <tr>
              <th className="px-4 py-3">Name</th>
              <th className="px-4 py-3">Email</th>
              <th className="px-4 py-3">Role</th>
              <th className="px-4 py-3">Status</th>
            </tr>
          </thead>
          <tbody className="divide-y divide-slate-100">
            {data?.data?.map((user: any) => (
              <tr key={user.id}>
                <td className="px-4 py-3 font-medium text-slate-900">{user.name}</td>
                <td className="px-4 py-3">{user.email}</td>
                <td className="px-4 py-3">{user.role}</td>
                <td className="px-4 py-3">
                  <span className={`rounded-full px-2 py-1 text-xs font-semibold ${user.active ? 'bg-brand/10 text-brand' : 'bg-slate-100 text-slate-500'}`}>
                    {user.active ? 'Active' : 'Inactive'}
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
