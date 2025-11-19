'use client';

import Link from 'next/link';
import { usePathname } from 'next/navigation';
import { ReactNode } from 'react';
import clsx from 'clsx';

const nav = {
  superAdmin: [
    { href: '/(dashboard)/super-admin', label: 'Dashboard' },
    { href: '/(dashboard)/super-admin/plan-periods', label: 'Plan Periods' },
    { href: '/(dashboard)/super-admin/supply-plans', label: 'Supply Plans' },
    { href: '/(dashboard)/super-admin/deliveries', label: 'Deliveries' },
    { href: '/(dashboard)/super-admin/masters', label: 'Masters' },
    { href: '/(dashboard)/super-admin/users', label: 'Users' },
  ],
  field: [
    { href: '/(dashboard)/field-staff', label: 'My Dashboard' },
    { href: '/(dashboard)/field-staff/new-delivery', label: 'New Delivery' },
    { href: '/(dashboard)/field-staff/my-deliveries', label: 'My Deliveries' },
  ],
};

export function LayoutShell({ children, role = 'SUPER_ADMIN' }: { children: ReactNode; role?: 'SUPER_ADMIN' | 'FIELD_STAFF' }) {
  const pathname = usePathname();
  const items = role === 'SUPER_ADMIN' ? nav.superAdmin : nav.field;
  return (
    <div className="flex min-h-screen">
      <aside className="w-64 bg-white shadow-lg">
        <div className="p-6 border-b border-slate-100">
          <p className="text-lg font-semibold text-brand">Monrita</p>
          <p className="text-xs text-slate-500">Supply Intelligence</p>
        </div>
        <nav className="p-4 space-y-1">
          {items.map((item) => (
            <Link
              key={item.href}
              href={item.href}
              className={clsx(
                'block rounded-md px-3 py-2 text-sm font-medium transition',
                pathname === item.href ? 'bg-brand text-white' : 'text-slate-600 hover:bg-slate-100'
              )}
            >
              {item.label}
            </Link>
          ))}
        </nav>
      </aside>
      <main className="flex-1 bg-slate-50">
        <header className="flex items-center justify-between border-b border-slate-200 bg-white px-8 py-4">
          <div>
            <p className="text-xs uppercase tracking-wide text-slate-500">Active Plan Period</p>
            <p className="font-semibold text-slate-900">2025 · Term 1</p>
          </div>
          <button className="rounded-full bg-brand px-4 py-2 text-sm font-semibold text-white shadow-md">Sync</button>
        </header>
        <div className="p-8">{children}</div>
      </main>
    </div>
  );
}
