<?php

declare(strict_types=1);

namespace App\Repositories;

final class ProgramRepository extends BaseRepository
{
    protected function table(): string
    {
        return 'programs';
    }
}
