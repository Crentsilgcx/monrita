<?php

declare(strict_types=1);

namespace App\Repositories;

final class AssessmentAttemptRepository extends BaseRepository
{
    protected function table(): string
    {
        return 'assessment_attempts';
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function forLearner(int $learnerId): array
    {
        return array_values(array_filter(
            $this->all(),
            static fn (array $attempt): bool => (int) $attempt['learner_id'] === $learnerId
        ));
    }
}
