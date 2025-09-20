<?php

declare(strict_types=1);

namespace App\Repositories;

final class CareerRepository extends BaseRepository
{
    protected function table(): string
    {
        return 'careers';
    }
}
