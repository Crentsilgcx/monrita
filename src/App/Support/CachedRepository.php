<?php

declare(strict_types=1);

namespace App\Support;

use App\Repositories\BaseRepository;

final class CachedRepository
{
    public function __construct(
        private Cache $cache,
        private BaseRepository $repository,
    ) {
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    public function all(string $key, int $seconds = 120): array
    {
        return $this->cache->remember($key, $seconds, fn () => $this->repository->all());
    }

    public function forget(string $prefix): void
    {
        $this->cache->forgetByPrefix($prefix);
    }
}
