<?php

declare(strict_types=1);

namespace App\Support;

use App\Repositories\UserRepository;

final class Auth
{
    public function __construct(
        private SessionManager $session,
        private UserRepository $users,
    ) {
    }

    public function user(): ?array
    {
        $id = $this->session->get('user_id');
        if ($id === null) {
            return null;
        }

        return $this->users->find((int) $id);
    }

    public function attempt(string $email, string $password): bool
    {
        $user = $this->users->findByEmail($email);
        if ($user && password_verify($password, $user['password'])) {
            $this->session->regenerate();
            $this->session->put('user_id', $user['id']);

            return true;
        }

        return false;
    }

    public function logout(): void
    {
        $this->session->flush();
    }

    public function checkPermission(string $permission): bool
    {
        $user = $this->user();
        if (!$user) {
            return false;
        }

        if (in_array('super_admin', $user['roles'], true)) {
            return true;
        }

        $permissions = $this->users->permissionsForUser((int) $user['id']);

        return in_array($permission, $permissions, true);
    }

    public function hasRole(string $role): bool
    {
        $user = $this->user();
        if (!$user) {
            return false;
        }

        return in_array($role, $user['roles'], true);
    }
}
