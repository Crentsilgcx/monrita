<?php

declare(strict_types=1);

namespace App\Repositories;

final class LearnerSkillRepository extends BaseRepository
{
    protected function table(): string
    {
        return 'learner_skills';
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function forLearner(int $learnerId): array
    {
        return array_values(array_filter(
            $this->all(),
            static fn (array $skill): bool => (int) $skill['learner_id'] === $learnerId
        ));
    }
}
