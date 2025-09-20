<?php

declare(strict_types=1);

namespace App\Repositories;

final class LessonProgressRepository extends BaseRepository
{
    protected function table(): string
    {
        return 'lesson_progress';
    }

    public function findForLessonUser(int $lessonId, int $userId): ?array
    {
        foreach ($this->all() as $progress) {
            if ((int) $progress['lesson_id'] === $lessonId && (int) $progress['user_id'] === $userId) {
                return $progress;
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
            static fn (array $progress): bool => (int) $progress['user_id'] === $userId
        ));
    }
}
