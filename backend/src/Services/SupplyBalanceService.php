<?php

namespace App\Services;

use App\Config\Database;
use App\Repositories\SupplyPlanRepository;
use App\Services\Cache\RequestCache;
use App\Services\PlanPeriodMetricsService;
use PDO;
use PDOException;

class SupplyBalanceService
{
    private PDO $db;
    private SupplyPlanRepository $supplyPlanRepository;
    private PlanPeriodMetricsService $metricsService;

    public function __construct()
    {
        $this->db = Database::getConnection();
        $this->supplyPlanRepository = new SupplyPlanRepository();
        $this->metricsService = new PlanPeriodMetricsService($this->db);
    }

    public function applyDelivery(array $payload): array
    {
        $this->db->beginTransaction();
        try {
            $planRow = $this->supplyPlanRepository->getPlanRowForUpdate(
                $payload['plan_period_id'],
                $payload['school_id'],
                $payload['commodity_id']
            );
            if (!$planRow) {
                throw new PDOException('Supply plan not found for combination');
            }
            $remaining = $planRow['remaining_quantity'];
            if ($remaining === null) {
                $remaining = $planRow['planned_quantity'];
            }

            $expectedBefore = (float) $remaining;
            $actualQuantity = (float) $payload['actual_quantity'];
            if ($actualQuantity <= 0) {
                throw new PDOException('Quantity must be positive');
            }
            if ($actualQuantity > $expectedBefore) {
                throw new PDOException('Delivery exceeds remaining quantity');
            }
            $remainingAfter = $expectedBefore - $actualQuantity;

            $deliveryStmt = $this->db->prepare('INSERT INTO deliveries (plan_period_id, school_id, commodity_id, supplier_id, user_id, delivery_date, expected_quantity_before, actual_quantity, remaining_quantity_after, notes) VALUES (:plan_period_id, :school_id, :commodity_id, :supplier_id, :user_id, :delivery_date, :expected_quantity_before, :actual_quantity, :remaining_quantity_after, :notes)');
            $deliveryStmt->execute([
                'plan_period_id' => $payload['plan_period_id'],
                'school_id' => $payload['school_id'],
                'commodity_id' => $payload['commodity_id'],
                'supplier_id' => $payload['supplier_id'] ?? null,
                'user_id' => $payload['user_id'],
                'delivery_date' => $payload['delivery_date'],
                'expected_quantity_before' => $expectedBefore,
                'actual_quantity' => $actualQuantity,
                'remaining_quantity_after' => $remainingAfter,
                'notes' => $payload['notes'] ?? null,
            ]);

            $updatePlan = $this->db->prepare('UPDATE supply_plans SET remaining_quantity = :remaining WHERE id = :id');
            $updatePlan->execute([
                'remaining' => $remainingAfter,
                'id' => $planRow['id'],
            ]);

            $cacheKey = 'plan_balance:' . $payload['plan_period_id'] . ':' . $payload['school_id'] . ':' . $payload['commodity_id'];
            RequestCache::put($cacheKey, [
                'planned_quantity' => $planRow['planned_quantity'],
                'remaining_quantity' => $remainingAfter,
            ]);

            $this->metricsService->applyDeliveryDelta($payload['plan_period_id'], $actualQuantity);

            $this->db->commit();

            return [
                'expected_before' => $expectedBefore,
                'remaining_after' => $remainingAfter,
            ];
        } catch (PDOException $e) {
            $this->db->rollBack();
            throw $e;
        }
    }
}
