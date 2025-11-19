'use client';

import { Fragment } from 'react';

export type ExperienceFeature = {
  id: string;
  title: string;
  description: string;
  definition: string;
  tip: string;
  target: 'admin' | 'field' | 'global';
  metric: string;
};

const PRO_FEATURES: ExperienceFeature[] = [
  {
    id: 'mission-control',
    title: 'Mission Control Snapshot',
    description: 'Condenses performance, alerts, and focus anchors for super admins in a single sentence.',
    definition: 'A real-time digest that translates telemetry into one executive message.',
    tip: 'Use it to anchor leadership stand-ups and highlight blockers early.',
    target: 'admin',
    metric: '98% confidence',
  },
  {
    id: 'service-health',
    title: 'Service Health Dial',
    description: 'Color-coded gauge outlining the quality of data intake versus plan commitments.',
    definition: 'Aggregated health based on timeliness, completeness, and reconciliation accuracy.',
    tip: 'Keep the needle above 85% before triggering replenishment decisions.',
    target: 'admin',
    metric: '87% healthy',
  },
  {
    id: 'resilience-radar',
    title: 'Resilience Radar',
    description: 'Maps sensitive regions and commodities to proactively stage buffers.',
    definition: 'Risk grid derived from plan variance and supplier stability scores.',
    tip: 'Hover to read mitigations and communicate them to logistics partners.',
    target: 'admin',
    metric: '12 hotspots',
  },
  {
    id: 'compliance-pulse',
    title: 'Compliance Pulse',
    description: 'Explains how delivery photos, signatures, and GPS traces are trending.',
    definition: 'Blend of verification evidence, giving one transparent audit rating.',
    tip: 'Stay green to simplify donor audits and regulatory reviews.',
    target: 'admin',
    metric: 'A · Fully trusted',
  },
  {
    id: 'decision-diary',
    title: 'Decision Diary',
    description: 'Quick journal of manual overrides or schedule shifts, stamped with rationale.',
    definition: 'Micro log intentionally exposed so admins remember why adjustments occurred.',
    tip: 'Log context immediately to avoid escalations later.',
    target: 'admin',
    metric: '3 notes today',
  },
  {
    id: 'supplier-spotlight',
    title: 'Supplier Spotlight',
    description: 'Highlights vendor reliability, current loads, and contact cards.',
    definition: 'Mini CRM module summarizing the last three deliveries per supplier.',
    tip: 'Escalate early if reliability dips below 70%.',
    target: 'admin',
    metric: 'Top partner: GreenFoods',
  },
  {
    id: 'response-board',
    title: 'Response Board',
    description: 'Timeline of escalations, resolved issues, and owners.',
    definition: 'Pulls from incident labels to show whether schools are unblocked.',
    tip: 'Assign champions next to every open tile.',
    target: 'admin',
    metric: '2 urgent plays',
  },
  {
    id: 'delivery-coach',
    title: 'Delivery Coach',
    description: 'Translates plan math into plain language for field staff, with reminders and nudges.',
    definition: 'Conversational helper summarizing balances before they submit.',
    tip: 'Read the reminder text aloud during site visits to avoid mistakes.',
    target: 'field',
    metric: 'Coach ready',
  },
  {
    id: 'campus-navigator',
    title: 'Campus Navigator',
    description: 'Suggests recently visited schools plus their contact persons.',
    definition: 'Smart list of last five campuses, sorted by urgency.',
    tip: 'Tap a campus tile to auto-fill the delivery form.',
    target: 'field',
    metric: '5 priority schools',
  },
  {
    id: 'pack-prep',
    title: 'Pack Prep Checklist',
    description: 'Confirms documentation, photos, and weighing steps before dispatch.',
    definition: 'A living checklist referencing compliance policies.',
    tip: 'Green ticks mean you can hit submit with confidence.',
    target: 'field',
    metric: '4/5 ready',
  },
  {
    id: 'risk-decoder',
    title: 'Risk Decoder',
    description: 'Teaches what late, partial, or damaged deliveries mean in the platform.',
    definition: 'Mini glossary so terminology stays consistent with HQ.',
    tip: 'Share definitions with school bursars to stay aligned.',
    target: 'field',
    metric: 'Clear definitions',
  },
  {
    id: 'micro-learning',
    title: 'Micro-Learning Capsules',
    description: '30-second reads that define a single KPI or workflow.',
    definition: 'Snackable lessons curated by the national support team.',
    tip: 'Bookmark capsules that resonate with your daily route.',
    target: 'field',
    metric: 'New lesson: Variance math',
  },
  {
    id: 'insight-slate',
    title: 'Insight Slate',
    description: 'Global storyboard layering visuals, stats, and recommended actions.',
    definition: 'Guided layout bridging quantitative KPIs with qualitative advice.',
    tip: 'Scroll horizontally to reveal next priorities.',
    target: 'global',
    metric: 'Updated hourly',
  },
  {
    id: 'glossary-ribbon',
    title: 'Glossary Ribbon',
    description: 'Row of tappable badges that define core supply terms at a glance.',
    definition: 'Helps new teammates align vocabulary across channels.',
    tip: 'Pin the ribbon open during onboarding weeks.',
    target: 'global',
    metric: '12 shared terms',
  },
  {
    id: 'adoption-guide',
    title: 'Adoption Guide',
    description: 'Explains how to install, sync, and work offline in three digestible cards.',
    definition: 'Part tutorial, part readiness scan for the PWA experience.',
    tip: 'Walk new staff through the cards before handing over credentials.',
    target: 'global',
    metric: 'Offline mode: ready',
  },
];

function FeatureCard({ feature }: { feature: ExperienceFeature }) {
  return (
    <div className="flex flex-col rounded-2xl border border-white/10 bg-white/10 p-4 text-white backdrop-blur">
      <div className="flex items-center justify-between text-xs uppercase tracking-wide text-white/70">
        <span>{feature.title}</span>
        <span className="rounded-full bg-white/20 px-2 py-0.5">{feature.metric}</span>
      </div>
      <p className="mt-2 text-sm text-white/90">{feature.description}</p>
      <dl className="mt-3 space-y-1 text-xs text-white/80">
        <div>
          <dt className="font-semibold">Definition</dt>
          <dd>{feature.definition}</dd>
        </div>
        <div>
          <dt className="font-semibold">Pro tip</dt>
          <dd>{feature.tip}</dd>
        </div>
      </dl>
    </div>
  );
}

export function AdminExperienceDeck() {
  const adminFeatures = PRO_FEATURES.filter((feature) => feature.target === 'admin');
  return (
    <section className="mt-10 rounded-3xl bg-slate-900 p-6 text-white shadow-xl">
      <div className="flex flex-col gap-2 md:flex-row md:items-center md:justify-between">
        <div>
          <p className="text-xs uppercase tracking-widest text-emerald-200">Admin experience suite</p>
          <h2 className="text-2xl font-semibold">15-point professional accelerator</h2>
          <p className="text-sm text-white/70">
            Engineered guidance keeps the national control room decisive, transparent, and easy to onboard.
          </p>
        </div>
        <span className="rounded-full bg-emerald-500/20 px-3 py-1 text-xs font-semibold text-emerald-200">7 admin insights</span>
      </div>
      <div className="mt-6 grid gap-4 md:grid-cols-2 xl:grid-cols-3">
        {adminFeatures.map((feature) => (
          <FeatureCard key={feature.id} feature={feature} />
        ))}
      </div>
    </section>
  );
}

export function FieldExperienceCoach() {
  const fieldFeatures = PRO_FEATURES.filter((feature) => feature.target === 'field');
  return (
    <section className="mt-10 rounded-3xl border border-slate-100 bg-white p-6 shadow-xl">
      <header className="flex flex-col gap-2 md:flex-row md:items-center md:justify-between">
        <div>
          <p className="text-xs uppercase tracking-widest text-brand">Field staff cockpit</p>
          <h2 className="text-2xl font-semibold text-slate-900">Precision support for every visit</h2>
          <p className="text-sm text-slate-500">
            These cues summarize balances, checklists, and glossary notes so agents stay confident offline.
          </p>
        </div>
        <span className="rounded-full bg-brand/10 px-3 py-1 text-xs font-semibold text-brand">5 live coaching cues</span>
      </header>
      <div className="mt-6 grid gap-4 md:grid-cols-2">
        {fieldFeatures.map((feature) => (
          <div key={feature.id} className="rounded-2xl border border-slate-100 p-4">
            <p className="text-xs uppercase tracking-wide text-slate-400">{feature.title}</p>
            <p className="mt-2 text-sm text-slate-600">{feature.description}</p>
            <p className="mt-3 text-xs font-semibold text-slate-500">Definition</p>
            <p className="text-sm text-slate-700">{feature.definition}</p>
            <p className="mt-2 text-xs font-semibold text-brand">Pro tip</p>
            <p className="text-sm text-slate-700">{feature.tip}</p>
          </div>
        ))}
      </div>
    </section>
  );
}

export function KnowledgeExplainers() {
  const knowledgeFeatures = PRO_FEATURES.filter((feature) => feature.target === 'global');
  return (
    <section className="rounded-3xl border border-dashed border-gold/40 bg-gold/5 p-6">
      <header className="mb-4 flex flex-col gap-2 md:flex-row md:items-center md:justify-between">
        <div>
          <p className="text-xs uppercase tracking-widest text-gold">Guided definitions</p>
          <h3 className="text-xl font-semibold text-slate-900">Shared language makes training effortless</h3>
        </div>
        <span className="rounded-full bg-white px-3 py-1 text-xs font-semibold text-gold">3 explainer cards</span>
      </header>
      <div className="grid gap-4 md:grid-cols-3">
        {knowledgeFeatures.map((feature) => (
          <div key={feature.id} className="rounded-2xl bg-white p-4 shadow-sm">
            <p className="text-xs uppercase tracking-wide text-slate-400">{feature.title}</p>
            <p className="mt-2 text-sm text-slate-600">{feature.description}</p>
            <p className="mt-3 text-xs font-semibold text-slate-500">Definition</p>
            <p className="text-sm text-slate-800">{feature.definition}</p>
            <p className="mt-3 text-xs font-semibold text-gold">Usage tip</p>
            <p className="text-sm text-slate-700">{feature.tip}</p>
          </div>
        ))}
      </div>
    </section>
  );
}

export function ExperienceSummaryList() {
  return (
    <div className="mt-8 space-y-3 rounded-2xl bg-slate-900/90 p-4 text-white">
      <p className="text-xs uppercase tracking-widest text-white/60">Professional polish highlights</p>
      {PRO_FEATURES.map((feature, index) => (
        <Fragment key={feature.id}>
          <div className="flex items-start justify-between gap-4">
            <div>
              <p className="text-sm font-semibold">{index + 1}. {feature.title}</p>
              <p className="text-xs text-white/70">{feature.tip}</p>
            </div>
            <span className="text-xs text-white/60">{feature.target}</span>
          </div>
          {index < PRO_FEATURES.length - 1 && <hr className="border-white/10" />}
        </Fragment>
      ))}
    </div>
  );
}
