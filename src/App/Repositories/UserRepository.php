<?php

declare(strict_types=1);

namespace App\Repositories;

final class UserRepository extends BaseRepository
{
    protected function table(): string
    {
        return 'users';
    }

    public function findByEmail(string $email): ?array
    {
        foreach ($this->all() as $user) {
            if (strcasecmp($user['email'], $email) === 0) {
                return $user;
            }
        }

        return null;
    }

    /**
     * @return string[]
     */
    public function permissionsForUser(int $userId): array
    {
        $user = $this->find($userId);
        if (!$user) {
            return [];
        }
        $rolePermissions = [];
        foreach ($user['roles'] as $roleSlug) {
            $rolePermissions = array_merge($rolePermissions, $this->permissionsForRole($roleSlug));
        }

        $direct = $user['permissions'] ?? [];

        return array_values(array_unique(array_merge($rolePermissions, $direct)));
    }

    /**
     * @return string[]
     */
    private function permissionsForRole(string $roleSlug): array
    {
        $roles = $this->store->read('roles');
        $role = null;
        foreach ($roles as $record) {
            if (($record['slug'] ?? null) === $roleSlug) {
                $role = $record;
                break;
            }
        }

        if ($role === null) {
            return [];
        }

        return $role['permissions'] ?? [];
    }
}
