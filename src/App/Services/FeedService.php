<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\FeedRepository;

final class FeedService
{
    public function __construct(private FeedRepository $feeds)
    {
    }

    public function publish(int $actorId, string $verb, string $entityType, string $entityId, string $audience): void
    {
        $this->feeds->create([
            'actor_user_id' => $actorId,
            'verb' => $verb,
            'entity_type' => $entityType,
            'entity_id' => $entityId,
            'audience' => $audience,
            'created_at' => (new \DateTimeImmutable())->format(DATE_ATOM),
        ]);
    }
}
