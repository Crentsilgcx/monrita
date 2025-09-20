<?php

declare(strict_types=1);

namespace App\Repositories;

final class EventRegistrationRepository extends BaseRepository
{
    protected function table(): string
    {
        return 'event_registrations';
    }

    public function findForEventUser(int $eventId, int $userId): ?array
    {
        foreach ($this->all() as $registration) {
            if ((int) $registration['event_id'] === $eventId && (int) $registration['user_id'] === $userId) {
                return $registration;
            }
        }

        return null;
    }
}
