import './globals.css';
import type { Metadata } from 'next';
import { ReactNode } from 'react';
import { QueryClientProvider } from '@tanstack/react-query';
import { queryClient } from '../lib/query-client';

export const metadata: Metadata = {
  title: 'Monrita Supply Tracking',
  description: 'Supply allocation and delivery tracking platform for schools',
  themeColor: '#0f766e',
};

export default function RootLayout({ children }: { children: ReactNode }) {
  return (
    <html lang="en">
      <body className="min-h-screen bg-slate-50 text-slate-900">
        <QueryClientProvider client={queryClient}>{children}</QueryClientProvider>
      </body>
    </html>
  );
}
