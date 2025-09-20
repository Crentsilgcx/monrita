<?php

declare(strict_types=1);

namespace App\Repositories;

final class LearnerAttributeRepository extends BaseRepository
{
    protected function table(): string
    {
        return 'learner_attributes';
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function forLearner(int $learnerId): array
    {
        return array_values(array_filter(
            $this->all(),
            static fn (array $attribute): bool => (int) $attribute['learner_id'] === $learnerId
        ));
    }
}
