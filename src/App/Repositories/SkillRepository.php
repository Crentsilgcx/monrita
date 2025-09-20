<?php

declare(strict_types=1);

namespace App\Repositories;

final class SkillRepository extends BaseRepository
{
    protected function table(): string
    {
        return 'skills';
    }
}
