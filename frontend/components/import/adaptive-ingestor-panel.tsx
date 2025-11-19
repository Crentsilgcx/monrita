'use client';

type Props = {
  summary?: {
    format?: string;
    chunk_size?: number;
    imported_rows?: number;
    skipped_rows?: number;
    headers?: string[];
  } | null;
};

const supportedFormats = [
  { label: 'JSON', example: '[{"school_code":"SCH1","commodity_code":"RICE","planned_quantity":120}]' },
  { label: 'CSV', example: 'school_code,commodity_code,planned_quantity' },
  { label: 'XLSX', example: 'Sheet with columns school_code, commodity_code, planned_quantity' },
];

export function AdaptiveIngestorPanel({ summary }: Props) {
  return (
    <section className="rounded-3xl border border-dashed border-brand/40 bg-brand/5 p-5">
      <header className="mb-4 flex flex-col gap-2 md:flex-row md:items-center md:justify-between">
        <div>
          <p className="text-xs uppercase tracking-widest text-brand">Self-adaptive ingestor</p>
          <h3 className="text-xl font-semibold text-slate-900">Upload JSON, CSV, or Excel without schema edits</h3>
          <p className="text-sm text-slate-600">We auto-detect headers, map synonyms, and pick the safest chunk size.</p>
        </div>
        {summary && (
          <span className="rounded-full bg-white px-4 py-1 text-xs font-semibold text-brand">
            {summary.format} · {summary.chunk_size} rows / batch
          </span>
        )}
      </header>
      <div className="grid gap-4 md:grid-cols-3">
        {supportedFormats.map((format) => (
          <div key={format.label} className="rounded-2xl border border-white/50 bg-white/70 p-4 shadow-sm">
            <p className="text-xs uppercase tracking-wide text-slate-400">{format.label}</p>
            <p className="mt-2 text-sm text-slate-700">{format.example}</p>
          </div>
        ))}
      </div>
      {summary && (
        <div className="mt-4 rounded-2xl bg-white/80 p-4 text-sm text-slate-700">
          <p className="font-semibold text-slate-900">Last upload</p>
          <p>
            Imported {summary.imported_rows ?? 0} rows • Skipped {summary.skipped_rows ?? 0}
          </p>
          {summary.headers && summary.headers.length > 0 && (
            <p className="mt-2 text-xs text-slate-500">Detected headers: {summary.headers.join(', ')}</p>
          )}
        </div>
      )}
    </section>
  );
}
