<?php

namespace App\Controllers;

use App\Repositories\UserRepository;
use App\Utils\Response;
use App\Utils\Validator;

class UsersController
{
    private UserRepository $repository;

    public function __construct()
    {
        $this->repository = new UserRepository();
    }

    public function index(array $request): void
    {
        $page = (int) ($_GET['page'] ?? 1);
        $perPage = (int) ($_GET['per_page'] ?? 20);
        Response::json($this->repository->paginate($page, $perPage));
    }

    public function store(array $request): void
    {
        $input = json_decode(file_get_contents('php://input'), true) ?? [];
        $errors = Validator::requireFields($input, ['name', 'email', 'password', 'role']);
        if ($errors) {
            Response::json(['errors' => $errors], 422);
            return;
        }
        $id = $this->repository->create($input);
        Response::json(['id' => $id], 201);
    }
}
