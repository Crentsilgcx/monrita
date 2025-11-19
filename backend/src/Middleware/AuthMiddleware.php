<?php

namespace App\Middleware;

use App\Services\AuthService;
use App\Utils\Response;

class AuthMiddleware
{
    public function handle(): callable
    {
        return function (array $request) {
            $headers = getallheaders();
            $authHeader = $headers['Authorization'] ?? $headers['authorization'] ?? null;
            if (!$authHeader || !str_starts_with($authHeader, 'Bearer ')) {
                Response::json(['error' => 'Unauthorized'], 401);
                return false;
            }
            $token = trim(substr($authHeader, 7));
            $authService = new AuthService();
            $user = $authService->validateToken($token);
            if (!$user) {
                Response::json(['error' => 'Invalid token'], 401);
                return false;
            }
            $request['user'] = $user;
            return $request;
        };
    }
}
