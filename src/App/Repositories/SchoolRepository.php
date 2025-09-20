<?php

declare(strict_types=1);

namespace App\Repositories;

final class SchoolRepository extends BaseRepository
{
    protected function table(): string
    {
        return 'schools';
    }
}
