<?php

declare(strict_types=1);

namespace App\Repositories;

final class ReactionRepository extends BaseRepository
{
    protected function table(): string
    {
        return 'reactions';
    }

    public function findForPostUser(int $postId, int $userId): ?array
    {
        foreach ($this->all() as $reaction) {
            if ((int) $reaction['post_id'] === $postId && (int) $reaction['user_id'] === $userId) {
                return $reaction;
            }
        }

        return null;
    }
}
