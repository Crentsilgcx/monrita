<?php

declare(strict_types=1);

namespace App\Repositories;

final class ModuleRepository extends BaseRepository
{
    protected function table(): string
    {
        return 'course_modules';
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function forCourse(int $courseId): array
    {
        return array_values(array_filter(
            $this->all(),
            static fn (array $module): bool => (int) $module['course_id'] === $courseId
        ));
    }
}
