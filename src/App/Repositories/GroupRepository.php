<?php

declare(strict_types=1);

namespace App\Repositories;

final class GroupRepository extends BaseRepository
{
    protected function table(): string
    {
        return 'groups';
    }
}
