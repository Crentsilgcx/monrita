<?php

namespace App\Controllers;

use App\Repositories\CrudRepository;
use App\Utils\Pagination;
use App\Utils\Response;
use App\Utils\Validator;

class MasterDataController
{
    private CrudRepository $repository;
    private array $required;

    public function __construct(string $table, array $required = [])
    {
        $this->repository = new CrudRepository($table);
        $this->required = $required;
    }

    public function index(array $request): void
    {
        [$page, $perPage] = Pagination::sanitize((int) ($_GET['page'] ?? 1), (int) ($_GET['per_page'] ?? 20));
        Response::json($this->repository->paginate($page, $perPage), 200, 30);
    }

    public function store(array $request): void
    {
        $input = json_decode(file_get_contents('php://input'), true) ?? [];
        $errors = Validator::requireFields($input, $this->required);
        if ($errors) {
            Response::json(['errors' => $errors], 422);
            return;
        }
        $input['created_at'] = $input['updated_at'] = date('Y-m-d H:i:s');
        $id = $this->repository->create($input);
        Response::json(['id' => $id], 201);
    }
}
