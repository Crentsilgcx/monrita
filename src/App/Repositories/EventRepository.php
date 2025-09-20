<?php

declare(strict_types=1);

namespace App\Repositories;

final class EventRepository extends BaseRepository
{
    protected function table(): string
    {
        return 'events';
    }
}
