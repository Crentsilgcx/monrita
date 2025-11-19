'use client';

import { LayoutShell } from '../../../../components/layout-shell';
import { FormEvent, useState } from 'react';
import api from '../../../../lib/api-client';
import { usePlanBalance } from '../../../../hooks/use-plan-balance';
import { KnowledgeExplainers } from '../../../../components/experience/pro-experience';

export default function NewDeliveryPage() {
  const [planPeriodId, setPlanPeriodId] = useState(1);
  const [schoolId, setSchoolId] = useState<number>();
  const [commodityId, setCommodityId] = useState<number>();
  const [quantity, setQuantity] = useState('');
  const [message, setMessage] = useState<string | null>(null);
  const { data: balance } = usePlanBalance({ plan_period_id: planPeriodId, school_id: schoolId, commodity_id: commodityId });

  const submit = async (event: FormEvent<HTMLFormElement>) => {
    event.preventDefault();
    setMessage(null);
    try {
      const payload = {
        plan_period_id: planPeriodId,
        school_id: Number(schoolId),
        commodity_id: Number(commodityId),
        delivery_date: new Date().toISOString().slice(0, 10),
        actual_quantity: Number(quantity),
      };
      const { data } = await api.post('/deliveries', payload);
      setMessage(`Delivery saved. Remaining balance ${data.remaining_after}`);
    } catch (err: any) {
      setMessage('Error saving delivery: ' + err.response?.data?.error ?? 'Unknown');
    }
  };

  return (
    <LayoutShell role="FIELD_STAFF">
      <form onSubmit={submit} className="mx-auto max-w-2xl space-y-6 rounded-2xl border border-slate-100 bg-white p-8 shadow-sm">
        <div>
          <h1 className="text-2xl font-bold text-slate-900">Capture delivery</h1>
          <p className="text-sm text-slate-500">Validated against live remaining balances.</p>
        </div>
        {message && <p className="rounded-lg bg-slate-100 px-4 py-3 text-sm text-slate-700">{message}</p>}
        <div className="grid gap-4 md:grid-cols-2">
          <label className="text-sm">
            <span className="text-slate-500">Plan period</span>
            <select value={planPeriodId} onChange={(e) => setPlanPeriodId(Number(e.target.value))} className="mt-1 w-full rounded-lg border border-slate-200 px-3 py-2">
              <option value={1}>2025 · Term 1</option>
            </select>
          </label>
          <label className="text-sm">
            <span className="text-slate-500">School ID</span>
            <input value={schoolId ?? ''} onChange={(e) => setSchoolId(Number(e.target.value))} className="mt-1 w-full rounded-lg border border-slate-200 px-3 py-2" placeholder="ID" />
          </label>
          <label className="text-sm">
            <span className="text-slate-500">Commodity ID</span>
            <input value={commodityId ?? ''} onChange={(e) => setCommodityId(Number(e.target.value))} className="mt-1 w-full rounded-lg border border-slate-200 px-3 py-2" placeholder="ID" />
          </label>
          <label className="text-sm">
            <span className="text-slate-500">Actual quantity</span>
            <input value={quantity} onChange={(e) => setQuantity(e.target.value)} className="mt-1 w-full rounded-lg border border-slate-200 px-3 py-2" placeholder="0.00" />
          </label>
        </div>
        <div className="rounded-xl bg-slate-50 p-4 text-sm">
          <p className="font-semibold text-slate-600">Plan telemetry</p>
          <div className="mt-2 grid gap-4 md:grid-cols-3">
            <div>
              <p className="text-xs uppercase text-slate-400">Planned</p>
              <p className="text-lg font-semibold text-slate-900">{balance?.planned_quantity ?? '--'}</p>
            </div>
            <div>
              <p className="text-xs uppercase text-slate-400">Remaining</p>
              <p className="text-lg font-semibold text-brand">{balance?.remaining_quantity ?? '--'}</p>
            </div>
            <div>
              <p className="text-xs uppercase text-slate-400">Predicted</p>
              <p className="text-lg font-semibold text-gold">{balance ? Number(balance.remaining_quantity ?? 0) - Number(quantity || 0) : '--'}</p>
            </div>
          </div>
        </div>
        <button type="submit" className="w-full rounded-lg bg-brand px-4 py-3 text-sm font-semibold text-white">
          Submit delivery
        </button>
      </form>
      <div className="mx-auto mt-8 max-w-4xl">
        <KnowledgeExplainers />
      </div>
    </LayoutShell>
  );
}
