<?php

namespace App\Controllers;

use App\Services\AuthService;
use App\Utils\Response;
use App\Utils\Validator;

class AuthController
{
    private AuthService $authService;

    public function __construct()
    {
        $this->authService = new AuthService();
    }

    public function login(): void
    {
        $input = json_decode(file_get_contents('php://input'), true) ?? [];
        $errors = Validator::requireFields($input, ['email', 'password']);
        if ($errors) {
            Response::json(['errors' => $errors], 422);
            return;
        }
        $result = $this->authService->attempt($input['email'], $input['password']);
        if (!$result) {
            Response::json(['error' => 'Invalid credentials'], 401);
            return;
        }
        Response::json($result);
    }

    public function me(array $request): void
    {
        Response::json(['user' => $request['user']]);
    }

    public function logout(): void
    {
        $headers = getallheaders();
        $authHeader = $headers['Authorization'] ?? $headers['authorization'] ?? '';
        $token = trim(str_replace('Bearer', '', $authHeader));
        if ($token) {
            $this->authService->logout($token);
        }
        Response::json(['message' => 'Logged out']);
    }
}
