<?php

namespace App\Repositories;

use App\Config\Database;
use App\Services\Cache\RequestCache;
use PDO;

class CrudRepository
{
    protected PDO $db;
    protected string $table;

    public function __construct(string $table)
    {
        $this->db = Database::getConnection();
        $this->table = $table;
    }

    public function paginate(int $page = 1, int $perPage = 20): array
    {
        $cacheKey = "{$this->table}:{$page}:{$perPage}";
        return RequestCache::remember($cacheKey, function () use ($page, $perPage) {
            $offset = ($page - 1) * $perPage;
            $stmt = $this->db->prepare("SELECT * FROM {$this->table} ORDER BY updated_at DESC LIMIT :limit OFFSET :offset");
            $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
            $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();
            $items = $stmt->fetchAll();
            $count = (int) $this->db->query("SELECT COUNT(*) FROM {$this->table}")->fetchColumn();
            return [
                'data' => $items,
                'pagination' => [
                    'page' => $page,
                    'per_page' => $perPage,
                    'total' => $count,
                ],
            ];
        });
    }

    public function create(array $data): int
    {
        $columns = array_keys($data);
        $placeholders = array_map(fn($col) => ':' . $col, $columns);
        $sql = sprintf('INSERT INTO %s (%s) VALUES (%s)', $this->table, implode(',', $columns), implode(',', $placeholders));
        $stmt = $this->db->prepare($sql);
        $stmt->execute($data);
        return (int) $this->db->lastInsertId();
    }

    public function update(int $id, array $data): bool
    {
        $assignments = array_map(fn($col) => "$col = :$col", array_keys($data));
        $sql = sprintf('UPDATE %s SET %s WHERE id = :id', $this->table, implode(',', $assignments));
        $stmt = $this->db->prepare($sql);
        $data['id'] = $id;
        return $stmt->execute($data);
    }

    public function find(int $id): ?array
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE id = :id LIMIT 1");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch() ?: null;
    }
}
