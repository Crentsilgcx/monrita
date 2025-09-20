<?php

declare(strict_types=1);

namespace App\Repositories;

final class CourseRepository extends BaseRepository
{
    protected function table(): string
    {
        return 'courses';
    }
}
