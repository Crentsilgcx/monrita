<?php

declare(strict_types=1);

namespace App\Repositories;

final class AssessmentQuestionRepository extends BaseRepository
{
    protected function table(): string
    {
        return 'assessment_questions';
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function forAssessment(int $assessmentId): array
    {
        return array_values(array_filter(
            $this->all(),
            static fn (array $question): bool => (int) $question['assessment_id'] === $assessmentId
        ));
    }
}
