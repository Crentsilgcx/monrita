<?php

namespace App\Controllers;

use App\Services\PlanPeriodMetricsService;
use App\Utils\Response;

class DashboardController
{
    private PlanPeriodMetricsService $metricsService;

    public function __construct()
    {
        $this->metricsService = new PlanPeriodMetricsService();
    }

    public function summary(array $request): void
    {
        $planPeriodId = (int) ($_GET['plan_period_id'] ?? 0);
        if (!$planPeriodId) {
            Response::json(['error' => 'plan_period_id is required'], 422);
            return;
        }
        $row = $this->metricsService->getSummary($planPeriodId);
        Response::json([
            'plan_period_id' => $planPeriodId,
            'total_planned' => (float) ($row['total_planned'] ?? 0),
            'total_delivered' => (float) ($row['total_delivered'] ?? 0),
            'total_remaining' => (float) ($row['total_remaining'] ?? 0),
            'total_schools' => (int) ($row['total_schools'] ?? 0),
            'total_commodities' => (int) ($row['total_commodities'] ?? 0),
        ], 200, 60);
    }
}
