<?php

namespace App\Controllers;

use App\Repositories\PlanImportRepository;
use App\Services\PlanImportService;
use App\Utils\Pagination;
use App\Utils\Response;

class SupplyPlanImportsController
{
    private PlanImportService $service;
    private PlanImportRepository $repository;

    public function __construct()
    {
        $this->service = new PlanImportService();
        $this->repository = new PlanImportRepository();
    }

    public function store(array $request): void
    {
        $planPeriodId = (int) ($_POST['plan_period_id'] ?? 0);
        $file = $_FILES['file'] ?? null;
        if (!$planPeriodId || !$file) {
            Response::json(['error' => 'plan_period_id and file are required'], 422);
            return;
        }
        if ($file['error'] !== UPLOAD_ERR_OK) {
            Response::json(['error' => 'Upload failed'], 422);
            return;
        }
        try {
            $result = $this->service->importFile($planPeriodId, $request['user']['id'], $file);
            Response::json(['message' => 'Import processed', 'summary' => $result], 201);
        } catch (\Throwable $e) {
            Response::json(['error' => $e->getMessage()], 422);
        }
    }

    public function index(array $request): void
    {
        [$page, $perPage] = Pagination::sanitize((int) ($_GET['page'] ?? 1), (int) ($_GET['per_page'] ?? 20));
        $imports = $this->repository->list($page, $perPage);
        Response::json($imports, 200, 10);
    }
}
