<?php

declare(strict_types=1);

namespace App\Repositories;

final class OrganizationRepository extends BaseRepository
{
    protected function table(): string
    {
        return 'organizations';
    }
}
