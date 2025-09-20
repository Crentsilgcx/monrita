<?php

declare(strict_types=1);

namespace App\Repositories;

final class RecommendationRepository extends BaseRepository
{
    protected function table(): string
    {
        return 'learner_recommendations';
    }

    public function findForLearner(int $learnerId): ?array
    {
        foreach ($this->all() as $recommendation) {
            if ((int) $recommendation['learner_id'] === $learnerId) {
                return $recommendation;
            }
        }

        return null;
    }
}
