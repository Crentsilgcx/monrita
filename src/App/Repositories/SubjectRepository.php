<?php

declare(strict_types=1);

namespace App\Repositories;

final class SubjectRepository extends BaseRepository
{
    protected function table(): string
    {
        return 'subjects';
    }
}
