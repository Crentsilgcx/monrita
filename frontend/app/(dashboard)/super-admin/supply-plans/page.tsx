'use client';

import { useMemo, useState } from 'react';
import { LayoutShell } from '../../../../components/layout-shell';
import { useMutation, useQuery, useQueryClient } from '@tanstack/react-query';
import api from '../../../../lib/api-client';
import { PlanMetricsPanel } from '../../../../components/insights/plan-metrics-panel';
import { AdaptiveIngestorPanel } from '../../../../components/import/adaptive-ingestor-panel';
import { usePlanMetrics } from '../../../../hooks/use-plan-metrics';

type PlanRow = {
  id: number;
  school_name: string;
  commodity_name: string;
  planned_quantity: number;
  remaining_quantity: number | null;
};

export default function SupplyPlansPage() {
  const queryClient = useQueryClient();
  const [selectedPeriod, setSelectedPeriod] = useState<string>('');
  const [file, setFile] = useState<File | null>(null);
  const [ingestSummary, setIngestSummary] = useState<any | null>(null);

  const { data: planPeriods } = useQuery({
    queryKey: ['plan-periods'],
    queryFn: async () => {
      const response = await api.get('/plan-periods');
      return response.data?.data ?? [];
    },
    staleTime: 5 * 60 * 1000,
  });

  const activePeriodId = selectedPeriod || planPeriods?.[0]?.id?.toString() || '';
  const { data: metrics } = usePlanMetrics(activePeriodId);

  const { data: plans, isFetching } = useQuery({
    queryKey: ['supply-plans', activePeriodId],
    queryFn: async () => {
      if (!activePeriodId) return { data: [] };
      const response = await api.get('/supply-plans', {
        params: { plan_period_id: activePeriodId, per_page: 100 },
      });
      return response.data;
    },
    enabled: Boolean(activePeriodId),
  });

  const { data: importHistory } = useQuery({
    queryKey: ['plan-imports'],
    queryFn: async () => {
      const response = await api.get('/supply-plan-imports', { params: { per_page: 10 } });
      return response.data;
    },
  });

  const uploadMutation = useMutation({
    mutationFn: async () => {
      if (!file || !activePeriodId) throw new Error('Missing period or file');
      const formData = new FormData();
      formData.append('plan_period_id', activePeriodId);
      formData.append('file', file);
      const response = await api.post('/supply-plan-imports', formData, {
        headers: { 'Content-Type': 'multipart/form-data' },
      });
      return response.data;
    },
    onSuccess: (payload: any) => {
      setFile(null);
      setIngestSummary(payload?.summary ?? null);
      queryClient.invalidateQueries({ queryKey: ['supply-plans', activePeriodId] });
      queryClient.invalidateQueries({ queryKey: ['plan-imports'] });
      queryClient.invalidateQueries({ queryKey: ['plan-metrics', activePeriodId] });
    },
  });

  const summary = useMemo(() => {
    const rows: PlanRow[] = plans?.data ?? [];
    return rows.reduce(
      (acc, row) => {
        const remaining = row.remaining_quantity ?? row.planned_quantity;
        return {
          planned: acc.planned + Number(row.planned_quantity ?? 0),
          remaining: acc.remaining + Number(remaining ?? 0),
        };
      },
      { planned: 0, remaining: 0 }
    );
  }, [plans]);

  return (
    <LayoutShell role="SUPER_ADMIN">
      <div className="flex flex-col gap-6">
        <div className="flex flex-col gap-3 lg:flex-row lg:items-center lg:justify-between">
          <div>
            <h1 className="text-2xl font-bold text-slate-900">Supply plan</h1>
            <p className="text-sm text-slate-500">Upload JSON, CSV, or Excel allocations and compare planned vs remaining balances.</p>
          </div>
          <div className="flex flex-col gap-2 text-sm text-slate-500">
            <label className="text-xs font-semibold uppercase tracking-wide text-slate-600">Plan period</label>
            <select
              value={activePeriodId}
              onChange={(event) => setSelectedPeriod(event.target.value)}
              className="rounded-lg border border-slate-200 px-3 py-2 text-sm focus:border-brand focus:outline-none"
            >
              {planPeriods?.map((period: any) => (
                <option key={period.id} value={period.id}>
                  {period.label}
                </option>
              ))}
            </select>
          </div>
        </div>

        <PlanMetricsPanel metrics={metrics ?? undefined} />

        <div className="grid gap-4 md:grid-cols-2">
          <div className="rounded-2xl border border-slate-100 bg-white p-5 shadow-sm">
            <p className="text-xs uppercase text-slate-500">Total planned</p>
            <p className="mt-2 text-3xl font-bold text-slate-900">{summary.planned.toLocaleString()}</p>
          </div>
          <div className="rounded-2xl border border-slate-100 bg-white p-5 shadow-sm">
            <p className="text-xs uppercase text-slate-500">Total remaining</p>
            <p className="mt-2 text-3xl font-bold text-brand">{summary.remaining.toLocaleString()}</p>
          </div>
        </div>

        <div className="rounded-2xl border border-dashed border-brand/30 bg-white/70 p-5 shadow-inner">
          <form
            onSubmit={(event) => {
              event.preventDefault();
              uploadMutation.mutate();
            }}
            className="flex flex-col gap-4"
          >
            <div>
              <label className="text-xs font-semibold uppercase tracking-wide text-slate-600">Upload JSON, CSV, or XLSX</label>
              <input
                type="file"
                accept=".json,.csv,application/json,text/csv,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"
                onChange={(event) => setFile(event.target.files?.[0] ?? null)}
                className="mt-2 w-full rounded-lg border border-slate-200 px-3 py-2 text-sm"
              />
              <p className="mt-1 text-xs text-slate-500">
                The ingestor maps <code>school_code</code>, <code>commodity_code</code>, and <code>planned_quantity</code> even if the
                columns are renamed.
              </p>
            </div>
            <div className="flex items-center gap-3">
              <button
                type="submit"
                disabled={!file || uploadMutation.isPending || !activePeriodId}
                className="inline-flex items-center justify-center rounded-lg bg-brand px-5 py-2 text-sm font-semibold text-white transition hover:bg-emerald-700 disabled:cursor-not-allowed disabled:opacity-60"
              >
                {uploadMutation.isPending ? 'Uploading…' : 'Import plan file'}
              </button>
              {uploadMutation.isSuccess && (
                <span className="text-sm text-emerald-700">Import complete.</span>
              )}
              {uploadMutation.isError && (
                <span className="text-sm text-rose-600">Failed: {(uploadMutation.error as Error).message}</span>
              )}
            </div>
          </form>
        </div>

        <AdaptiveIngestorPanel summary={ingestSummary ?? undefined} />

        <div className="overflow-hidden rounded-2xl border border-slate-100 bg-white shadow-sm">
          <table className="min-w-full divide-y divide-slate-100 text-sm">
            <thead className="bg-slate-50 text-left text-xs font-semibold uppercase tracking-wide text-slate-500">
              <tr>
                <th className="px-4 py-3">School</th>
                <th className="px-4 py-3">Commodity</th>
                <th className="px-4 py-3">Planned</th>
                <th className="px-4 py-3">Remaining</th>
              </tr>
            </thead>
            <tbody className="divide-y divide-slate-100">
              {(plans?.data ?? []).map((plan: PlanRow) => (
                <tr key={plan.id} className={isFetching ? 'animate-pulse' : ''}>
                  <td className="px-4 py-3">{plan.school_name}</td>
                  <td className="px-4 py-3">{plan.commodity_name}</td>
                  <td className="px-4 py-3 font-semibold">{plan.planned_quantity}</td>
                  <td className="px-4 py-3 text-brand">{plan.remaining_quantity ?? plan.planned_quantity}</td>
                </tr>
              ))}
            </tbody>
          </table>
        </div>

        <div className="rounded-2xl border border-slate-100 bg-white p-5 shadow-sm">
          <div className="mb-4 flex items-center justify-between">
            <h2 className="text-lg font-semibold text-slate-900">Recent imports</h2>
            <span className="text-xs uppercase text-slate-400">Latest 10</span>
          </div>
          <div className="space-y-3 text-sm">
            {(importHistory?.data ?? []).map((row: any) => (
              <div key={row.id} className="flex items-center justify-between rounded-xl border border-slate-100 px-4 py-3">
                <div>
                  <p className="font-semibold text-slate-900">{row.original_filename}</p>
                  <p className="text-xs text-slate-500">
                    {row.plan_period_label} • {row.imported_by_name}
                  </p>
                </div>
                <div className="text-right">
                  <p className="text-xs uppercase tracking-wide text-slate-400">Rows</p>
                  <p className="text-base font-semibold text-brand">{row.imported_rows}</p>
                  <p className="text-xs text-slate-500">{row.status}</p>
                </div>
              </div>
            ))}
          </div>
        </div>
      </div>
    </LayoutShell>
  );
}
