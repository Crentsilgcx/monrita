<?php

declare(strict_types=1);

namespace App\Repositories;

final class CommentRepository extends BaseRepository
{
    protected function table(): string
    {
        return 'comments';
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function forPost(int $postId): array
    {
        return array_values(array_filter(
            $this->all(),
            static fn (array $comment): bool => (int) $comment['post_id'] === $postId
        ));
    }
}
