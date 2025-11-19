<?php

namespace App\Controllers;

use App\Repositories\CrudRepository;
use App\Services\PlanPeriodMetricsService;
use App\Utils\Pagination;
use App\Utils\Response;
use App\Utils\Validator;

class PlanPeriodsController
{
    private CrudRepository $repository;
    private PlanPeriodMetricsService $metricsService;

    public function __construct()
    {
        $this->repository = new CrudRepository('plan_periods');
        $this->metricsService = new PlanPeriodMetricsService();
    }

    public function index(array $request): void
    {
        [$page, $perPage] = Pagination::sanitize((int) ($_GET['page'] ?? 1), (int) ($_GET['per_page'] ?? 20));
        Response::json($this->repository->paginate($page, $perPage), 200, 30);
    }

    public function store(array $request): void
    {
        $input = json_decode(file_get_contents('php://input'), true) ?? [];
        $errors = Validator::requireFields($input, ['label']);
        if ($errors) {
            Response::json(['errors' => $errors], 422);
            return;
        }
        $input['is_active'] = $input['is_active'] ?? 0;
        $input['created_at'] = $input['updated_at'] = date('Y-m-d H:i:s');
        $id = $this->repository->create($input);
        Response::json(['id' => $id], 201);
    }

    public function metrics(array $request): void
    {
        $planPeriodId = isset($request['params'][0]) ? (int) $request['params'][0] : 0;
        if (!$planPeriodId) {
            Response::json(['error' => 'Invalid plan period'], 422);
            return;
        }
        $summary = $this->metricsService->getSummary($planPeriodId);
        Response::json($summary, 200, 60);
    }
}
