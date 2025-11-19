<?php

namespace App\Services;

use App\Config\Database;
use App\Services\Cache\FileCache;
use PDO;

class PlanPeriodMetricsService
{
    private PDO $db;

    public function __construct(?PDO $connection = null)
    {
        $this->db = $connection ?: Database::getConnection();
    }

    public function getSummary(int $planPeriodId): array
    {
        $cacheKey = 'plan_metrics:' . $planPeriodId;
        return FileCache::remember($cacheKey, 60, function () use ($planPeriodId) {
            $stmt = $this->db->prepare('SELECT * FROM plan_period_metrics WHERE plan_period_id = :id LIMIT 1');
            $stmt->execute(['id' => $planPeriodId]);
            $row = $stmt->fetch();
            if (!$row) {
                $row = $this->syncFromPlans($planPeriodId);
            }
            return $row;
        });
    }

    public function syncFromPlans(int $planPeriodId): array
    {
        $summaryStmt = $this->db->prepare('SELECT 
            COALESCE(SUM(planned_quantity), 0) as total_planned,
            COALESCE(SUM(COALESCE(planned_quantity,0) - COALESCE(remaining_quantity, planned_quantity)), 0) as total_delivered,
            COALESCE(SUM(COALESCE(remaining_quantity, planned_quantity)), 0) as total_remaining
            FROM supply_plans WHERE plan_period_id = :id');
        $summaryStmt->execute(['id' => $planPeriodId]);
        $summary = $summaryStmt->fetch() ?: ['total_planned' => 0, 'total_delivered' => 0, 'total_remaining' => 0];

        $countStmt = $this->db->prepare('SELECT COUNT(DISTINCT school_id) as schools, COUNT(DISTINCT commodity_id) as commodities FROM supply_plans WHERE plan_period_id = :id');
        $countStmt->execute(['id' => $planPeriodId]);
        $counts = $countStmt->fetch() ?: ['schools' => 0, 'commodities' => 0];

        $payload = [
            'plan_period_id' => $planPeriodId,
            'total_planned' => (float) $summary['total_planned'],
            'total_delivered' => (float) $summary['total_delivered'],
            'total_remaining' => (float) $summary['total_remaining'],
            'total_schools' => (int) $counts['schools'],
            'total_commodities' => (int) $counts['commodities'],
        ];

        $sql = 'INSERT INTO plan_period_metrics (plan_period_id, total_planned, total_delivered, total_remaining, total_schools, total_commodities, last_recalculated_at)
                VALUES (:plan_period_id, :total_planned, :total_delivered, :total_remaining, :total_schools, :total_commodities, NOW())
                ON DUPLICATE KEY UPDATE total_planned = VALUES(total_planned), total_delivered = VALUES(total_delivered), total_remaining = VALUES(total_remaining), total_schools = VALUES(total_schools), total_commodities = VALUES(total_commodities), last_recalculated_at = NOW()';
        $stmt = $this->db->prepare($sql);
        $stmt->execute($payload);

        FileCache::put('plan_metrics:' . $planPeriodId, $payload, 60);
        return $payload;
    }

    public function applyDeliveryDelta(int $planPeriodId, float $actualQuantity): void
    {
        $sql = 'INSERT INTO plan_period_metrics (plan_period_id, total_planned, total_delivered, total_remaining, total_schools, total_commodities, last_recalculated_at)
                VALUES (:plan_period_id, 0, :delivered, GREATEST(0, 0 - :delivered), 0, 0, NOW())
                ON DUPLICATE KEY UPDATE total_delivered = total_delivered + VALUES(total_delivered), total_remaining = GREATEST(0, total_remaining - VALUES(total_delivered)), last_recalculated_at = NOW()';
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'plan_period_id' => $planPeriodId,
            'delivered' => $actualQuantity,
        ]);
        FileCache::forget('plan_metrics:' . $planPeriodId);
    }
}
