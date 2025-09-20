<?php

declare(strict_types=1);

namespace App\Repositories;

final class LessonRepository extends BaseRepository
{
    protected function table(): string
    {
        return 'lessons';
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function forModule(int $moduleId): array
    {
        return array_values(array_filter(
            $this->all(),
            static fn (array $lesson): bool => (int) $lesson['module_id'] === $moduleId
        ));
    }
}
