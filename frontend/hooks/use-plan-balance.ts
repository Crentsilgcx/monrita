'use client';

import { useQuery } from '@tanstack/react-query';
import api from '../lib/api-client';

export function usePlanBalance(params: { plan_period_id?: number; school_id?: number; commodity_id?: number }) {
  const enabled = Boolean(params.plan_period_id && params.school_id && params.commodity_id);
  return useQuery({
    queryKey: ['plan-balance', params],
    enabled,
    queryFn: async () => {
      const { data } = await api.get('/supply-plans/balance', { params });
      return data;
    },
    staleTime: 60_000,
  });
}
