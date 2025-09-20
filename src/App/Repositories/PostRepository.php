<?php

declare(strict_types=1);

namespace App\Repositories;

final class PostRepository extends BaseRepository
{
    protected function table(): string
    {
        return 'posts';
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function forGroup(int $groupId): array
    {
        return array_values(array_filter(
            $this->all(),
            static fn (array $post): bool => (int) $post['group_id'] === $groupId
        ));
    }
}
