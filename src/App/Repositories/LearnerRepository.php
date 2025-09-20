<?php

declare(strict_types=1);

namespace App\Repositories;

final class LearnerRepository extends BaseRepository
{
    protected function table(): string
    {
        return 'learners';
    }

    public function findByUserId(int $userId): ?array
    {
        foreach ($this->all() as $learner) {
            if ((int) $learner['user_id'] === $userId) {
                return $learner;
            }
        }

        return null;
    }
}
