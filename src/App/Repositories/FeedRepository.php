<?php

declare(strict_types=1);

namespace App\Repositories;

final class FeedRepository extends BaseRepository
{
    protected function table(): string
    {
        return 'feed_items';
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function latest(int $limit = 10): array
    {
        $items = $this->all();
        usort($items, static fn (array $a, array $b): int => strcmp($b['created_at'], $a['created_at']));

        return array_slice($items, 0, $limit);
    }
}
