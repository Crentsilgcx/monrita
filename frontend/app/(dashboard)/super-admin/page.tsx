'use client';

import { LayoutShell } from '../../../components/layout-shell';
import { useState } from 'react';
import { AdminExperienceDeck, ExperienceSummaryList } from '../../../components/experience/pro-experience';
import { usePlanMetrics } from '../../../hooks/use-plan-metrics';
import { PlanMetricsPanel } from '../../../components/insights/plan-metrics-panel';
import { PerformancePlaybook } from '../../../components/insights/performance-playbook';
import { FeatureGallery } from '../../../components/insights/feature-gallery';
import {
  ExecutiveHero,
  IntelligenceStack,
  DeliveryMomentumPanel,
  RiskAndConfidenceMatrix,
  StrategicActionGrid,
  ExecutiveNarratives,
} from '../../../components/super-admin/executive-suite';

export default function SuperAdminDashboard() {
  const [planPeriodId] = useState(1);
  const { data: metrics } = usePlanMetrics(planPeriodId);

  return (
    <LayoutShell role="SUPER_ADMIN">
      <ExecutiveHero metrics={metrics ?? undefined} />
      <PlanMetricsPanel metrics={metrics ?? undefined} />
      <IntelligenceStack />
      <DeliveryMomentumPanel metrics={metrics ?? undefined} />
      <RiskAndConfidenceMatrix />
      <StrategicActionGrid />
      <PerformancePlaybook />
      <AdminExperienceDeck />
      <ExperienceSummaryList />
      <FeatureGallery />
      <ExecutiveNarratives />
    </LayoutShell>
  );
}
