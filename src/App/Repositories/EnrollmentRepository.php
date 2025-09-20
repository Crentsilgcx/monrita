<?php

declare(strict_types=1);

namespace App\Repositories;

final class EnrollmentRepository extends BaseRepository
{
    protected function table(): string
    {
        return 'enrollments';
    }

    public function findForCourseUser(int $courseId, int $userId): ?array
    {
        foreach ($this->all() as $enrollment) {
            if ((int) $enrollment['course_id'] === $courseId && (int) $enrollment['user_id'] === $userId) {
                return $enrollment;
            }
        }

        return null;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function forUser(int $userId): array
    {
        return array_values(array_filter(
            $this->all(),
            static fn (array $enrollment): bool => (int) $enrollment['user_id'] === $userId
        ));
    }
}
