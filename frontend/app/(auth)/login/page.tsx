'use client';

import { FormEvent, useState } from 'react';
import api from '../../../lib/api-client';
import { useRouter } from 'next/navigation';

export default function LoginPage() {
  const router = useRouter();
  const [email, setEmail] = useState('admin@example.com');
  const [password, setPassword] = useState('password');
  const [error, setError] = useState<string | null>(null);
  const [loading, setLoading] = useState(false);

  const submit = async (event: FormEvent<HTMLFormElement>) => {
    event.preventDefault();
    setLoading(true);
    setError(null);
    try {
      const { data } = await api.post('/auth/login', { email, password });
      localStorage.setItem('monrita_token', data.token);
      localStorage.setItem('monrita_user', JSON.stringify(data.user));
      router.push('/(dashboard)/super-admin');
    } catch (err) {
      setError('Invalid credentials');
    } finally {
      setLoading(false);
    }
  };

  return (
    <div className="flex min-h-screen items-center justify-center bg-gradient-to-br from-brand/20 via-white to-gold/10 p-4">
      <form onSubmit={submit} className="w-full max-w-md space-y-4 rounded-2xl bg-white p-8 shadow-xl">
        <div>
          <p className="text-sm font-semibold uppercase tracking-wide text-brand">Monrita</p>
          <h1 className="text-2xl font-bold text-slate-900">Sign in to continue</h1>
          <p className="text-sm text-slate-500">Supply allocation & delivery intelligence</p>
        </div>
        {error && <p className="rounded-md bg-red-50 p-3 text-sm text-red-600">{error}</p>}
        <div className="space-y-1">
          <label className="text-sm font-medium text-slate-600">Email</label>
          <input
            className="w-full rounded-lg border border-slate-200 px-3 py-2 focus:border-brand focus:outline-none"
            value={email}
            onChange={(e) => setEmail(e.target.value)}
          />
        </div>
        <div className="space-y-1">
          <label className="text-sm font-medium text-slate-600">Password</label>
          <input
            type="password"
            className="w-full rounded-lg border border-slate-200 px-3 py-2 focus:border-brand focus:outline-none"
            value={password}
            onChange={(e) => setPassword(e.target.value)}
          />
        </div>
        <button
          type="submit"
          disabled={loading}
          className="w-full rounded-lg bg-brand px-4 py-2 font-semibold text-white shadow-lg transition hover:bg-brand/90 disabled:opacity-60"
        >
          {loading ? 'Authenticating...' : 'Sign in'}
        </button>
      </form>
    </div>
  );
}
