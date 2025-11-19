<?php

namespace App\Repositories;

use App\Config\Database;
use PDO;

class UserRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function paginate(int $page = 1, int $perPage = 20): array
    {
        $offset = ($page - 1) * $perPage;
        $stmt = $this->db->prepare('SELECT id, name, email, role, active, created_at FROM users ORDER BY created_at DESC LIMIT :limit OFFSET :offset');
        $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        $items = $stmt->fetchAll();

        $count = (int) $this->db->query('SELECT COUNT(*) FROM users')->fetchColumn();
        return [
            'data' => $items,
            'pagination' => [
                'page' => $page,
                'per_page' => $perPage,
                'total' => $count,
            ],
        ];
    }

    public function create(array $payload): int
    {
        $stmt = $this->db->prepare('INSERT INTO users (name, email, password, role, active) VALUES (:name, :email, :password, :role, :active)');
        $stmt->execute([
            'name' => $payload['name'],
            'email' => $payload['email'],
            'password' => password_hash($payload['password'], PASSWORD_BCRYPT),
            'role' => $payload['role'],
            'active' => $payload['active'] ?? 1,
        ]);
        return (int) $this->db->lastInsertId();
    }
}
