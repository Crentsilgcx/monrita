<?php

declare(strict_types=1);

namespace App\Repositories;

final class AssessmentResponseRepository extends BaseRepository
{
    protected function table(): string
    {
        return 'assessment_responses';
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function forAttempt(int $attemptId): array
    {
        return array_values(array_filter(
            $this->all(),
            static fn (array $response): bool => (int) $response['attempt_id'] === $attemptId
        ));
    }
}
