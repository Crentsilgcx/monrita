<?php

namespace App\Repositories;

use App\Config\Database;
use PDO;

class DeliveryRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function list(array $filters, int $page = 1, int $perPage = 20): array
    {
        $conditions = [];
        $params = [];

        foreach (['plan_period_id', 'school_id', 'commodity_id', 'supplier_id', 'user_id'] as $field) {
            if (!empty($filters[$field])) {
                $conditions[] = "d.$field = :$field";
                $params[$field] = $filters[$field];
            }
        }

        if (!empty($filters['date_from'])) {
            $conditions[] = 'd.delivery_date >= :date_from';
            $params['date_from'] = $filters['date_from'];
        }
        if (!empty($filters['date_to'])) {
            $conditions[] = 'd.delivery_date <= :date_to';
            $params['date_to'] = $filters['date_to'];
        }

        $where = $conditions ? 'WHERE ' . implode(' AND ', $conditions) : '';
        $offset = ($page - 1) * $perPage;

        $sql = "SELECT d.*, s.name as school_name, c.name as commodity_name, u.name as user_name FROM deliveries d
                JOIN schools s ON s.id = d.school_id
                JOIN commodities c ON c.id = d.commodity_id
                JOIN users u ON u.id = d.user_id
                $where ORDER BY d.delivery_date DESC LIMIT :limit OFFSET :offset";

        $stmt = $this->db->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue(':' . $key, $value);
        }
        $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        $items = $stmt->fetchAll();

        $countStmt = $this->db->prepare('SELECT COUNT(*) FROM deliveries d ' . $where);
        foreach ($params as $key => $value) {
            $countStmt->bindValue(':' . $key, $value);
        }
        $countStmt->execute();
        $count = (int) $countStmt->fetchColumn();

        return [
            'data' => $items,
            'pagination' => [
                'page' => $page,
                'per_page' => $perPage,
                'total' => $count,
            ],
        ];
    }
}
