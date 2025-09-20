<?php

declare(strict_types=1);

namespace App\Repositories;

final class CareerSkillRepository extends BaseRepository
{
    protected function table(): string
    {
        return 'career_skills';
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function forCareer(int $careerId): array
    {
        return array_values(array_filter(
            $this->all(),
            static fn (array $record): bool => (int) $record['career_id'] === $careerId
        ));
    }
}
