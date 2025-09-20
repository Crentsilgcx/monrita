<?php

declare(strict_types=1);

namespace App\Repositories;

final class AssessmentRepository extends BaseRepository
{
    protected function table(): string
    {
        return 'assessments';
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function active(): array
    {
        return array_values(array_filter(
            $this->all(),
            static fn (array $assessment): bool => ($assessment['is_active'] ?? false) === true
        ));
    }
}
