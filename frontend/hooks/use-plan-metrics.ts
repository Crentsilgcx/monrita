import { useQuery } from '@tanstack/react-query';
import api from '../lib/api-client';

export function usePlanMetrics(planPeriodId?: string | number | null) {
  return useQuery({
    queryKey: ['plan-metrics', planPeriodId],
    enabled: Boolean(planPeriodId),
    queryFn: async () => {
      if (!planPeriodId) return null;
      const response = await api.get(`/plan-periods/${planPeriodId}/metrics`);
      return response.data;
    },
    staleTime: 60 * 1000,
  });
}
