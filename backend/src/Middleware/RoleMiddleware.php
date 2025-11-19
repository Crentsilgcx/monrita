<?php

namespace App\Middleware;

use App\Utils\Response;

class RoleMiddleware
{
    public function handle(array $allowedRoles): callable
    {
        return function (array $request) use ($allowedRoles) {
            $user = $request['user'] ?? null;
            if (!$user || !in_array($user['role'], $allowedRoles, true)) {
                Response::json(['error' => 'Forbidden'], 403);
                return false;
            }
            return $request;
        };
    }
}
