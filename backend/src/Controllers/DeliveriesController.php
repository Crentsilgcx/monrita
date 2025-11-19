<?php

namespace App\Controllers;

use App\Repositories\DeliveryRepository;
use App\Repositories\SupplyPlanRepository;
use App\Services\SupplyBalanceService;
use App\Utils\Pagination;
use App\Utils\Response;
use App\Utils\Validator;
use PDOException;

class DeliveriesController
{
    private DeliveryRepository $deliveryRepository;
    private SupplyBalanceService $balanceService;
    private SupplyPlanRepository $planRepository;

    public function __construct()
    {
        $this->deliveryRepository = new DeliveryRepository();
        $this->balanceService = new SupplyBalanceService();
        $this->planRepository = new SupplyPlanRepository();
    }

    public function index(array $request): void
    {
        $user = $request['user'];
        $filters = [
            'plan_period_id' => $_GET['plan_period_id'] ?? null,
            'school_id' => $_GET['school_id'] ?? null,
            'commodity_id' => $_GET['commodity_id'] ?? null,
            'supplier_id' => $_GET['supplier_id'] ?? null,
            'date_from' => $_GET['date_from'] ?? null,
            'date_to' => $_GET['date_to'] ?? null,
        ];
        if ($user['role'] !== 'SUPER_ADMIN') {
            $filters['user_id'] = $user['id'];
        } else {
            $filters['user_id'] = $_GET['user_id'] ?? null;
        }
        [$page, $perPage] = Pagination::sanitize((int) ($_GET['page'] ?? 1), (int) ($_GET['per_page'] ?? 20));
        Response::json($this->deliveryRepository->list($filters, $page, $perPage), 200, 5);
    }

    public function store(array $request): void
    {
        $user = $request['user'];
        $input = json_decode(file_get_contents('php://input'), true) ?? [];
        $errors = Validator::requireFields($input, ['plan_period_id', 'school_id', 'commodity_id', 'delivery_date', 'actual_quantity']);
        if ($errors) {
            Response::json(['errors' => $errors], 422);
            return;
        }
        $payload = [
            'plan_period_id' => (int) $input['plan_period_id'],
            'school_id' => (int) $input['school_id'],
            'commodity_id' => (int) $input['commodity_id'],
            'supplier_id' => isset($input['supplier_id']) ? (int) $input['supplier_id'] : null,
            'delivery_date' => $input['delivery_date'],
            'actual_quantity' => (float) $input['actual_quantity'],
            'notes' => $input['notes'] ?? null,
            'user_id' => $user['id'],
        ];
        try {
            $result = $this->balanceService->applyDelivery($payload);
            Response::json([
                'message' => 'Delivery recorded',
                'expected_before' => $result['expected_before'],
                'remaining_after' => $result['remaining_after'],
            ], 201);
        } catch (PDOException $e) {
            Response::json(['error' => $e->getMessage()], 422);
        }
    }

    public function balance(array $request): void
    {
        $planPeriodId = (int) ($_GET['plan_period_id'] ?? 0);
        $schoolId = (int) ($_GET['school_id'] ?? 0);
        $commodityId = (int) ($_GET['commodity_id'] ?? 0);
        if (!$planPeriodId || !$schoolId || !$commodityId) {
            Response::json(['error' => 'Missing identifiers'], 422);
            return;
        }
        $balance = $this->planRepository->findBalance($planPeriodId, $schoolId, $commodityId);
        if (!$balance) {
            Response::json(['error' => 'Plan row not found'], 404);
            return;
        }
        Response::json($balance, 200, 15);
    }
}
