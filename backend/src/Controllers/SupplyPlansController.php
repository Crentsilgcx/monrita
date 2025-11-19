<?php

namespace App\Controllers;

use App\Repositories\SupplyPlanRepository;
use App\Utils\Pagination;
use App\Utils\Response;
use App\Utils\Validator;

class SupplyPlansController
{
    private SupplyPlanRepository $repository;

    public function __construct()
    {
        $this->repository = new SupplyPlanRepository();
    }

    public function index(array $request): void
    {
        $filters = [
            'plan_period_id' => $_GET['plan_period_id'] ?? null,
            'school_id' => $_GET['school_id'] ?? null,
            'commodity_id' => $_GET['commodity_id'] ?? null,
        ];
        [$page, $perPage] = Pagination::sanitize((int) ($_GET['page'] ?? 1), (int) ($_GET['per_page'] ?? 20));
        Response::json($this->repository->list($filters, $page, $perPage), 200, 30);
    }

    public function store(array $request): void
    {
        $input = json_decode(file_get_contents('php://input'), true) ?? [];
        $errors = Validator::requireFields($input, ['plan_period_id', 'school_id', 'commodity_id', 'planned_quantity']);
        if ($errors) {
            Response::json(['errors' => $errors], 422);
            return;
        }
        $row = [[
            'plan_period_id' => (int) $input['plan_period_id'],
            'school_id' => (int) $input['school_id'],
            'commodity_id' => (int) $input['commodity_id'],
            'planned_quantity' => (float) $input['planned_quantity'],
            'notes' => $input['notes'] ?? null,
        ]];
        $this->repository->bulkUpsert($row);
        Response::json(['message' => 'Supply plan saved'], 201);
    }
}
