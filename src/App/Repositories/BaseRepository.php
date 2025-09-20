<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Support\JsonStore;

abstract class BaseRepository
{
    public function __construct(
        protected JsonStore $store,
    ) {
    }

    abstract protected function table(): string;

    /**
     * @return array<int, array<string, mixed>>
     */
    public function all(): array
    {
        return array_values($this->store->read($this->table()));
    }

    public function find(int $id): ?array
    {
        $records = $this->store->read($this->table());

        return $records[(string) $id] ?? null;
    }

    /**
     * @param array<string, mixed> $attributes
     */
    public function create(array $attributes): array
    {
        $records = $this->store->read($this->table());
        $id = $attributes['id'] ?? $this->nextId($records);
        $attributes['id'] = $id;
        $records[(string) $id] = $attributes;
        $this->store->write($this->table(), $records);

        return $attributes;
    }

    /**
     * @param array<string, mixed> $attributes
     */
    public function update(int $id, array $attributes): ?array
    {
        $records = $this->store->read($this->table());
        if (!isset($records[(string) $id])) {
            return null;
        }
        $records[(string) $id] = array_merge($records[(string) $id], $attributes);
        $this->store->write($this->table(), $records);

        return $records[(string) $id];
    }

    public function delete(int $id): void
    {
        $records = $this->store->read($this->table());
        unset($records[(string) $id]);
        $this->store->write($this->table(), $records);
    }

    /**
     * @param array<int|string, mixed> $records
     */
    protected function nextId(array $records): int
    {
        if ($records === []) {
            return 1;
        }

        return max(array_map('intval', array_keys($records))) + 1;
    }
}
