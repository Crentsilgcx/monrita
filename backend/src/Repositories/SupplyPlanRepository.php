<?php

namespace App\Repositories;

use App\Config\Database;
use App\Services\Cache\RequestCache;
use App\Services\Performance\StatementCache;
use PDO;

class SupplyPlanRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function findBalance(int $planPeriodId, int $schoolId, int $commodityId): ?array
    {
        $cacheKey = "plan_balance:{$planPeriodId}:{$schoolId}:{$commodityId}";
        return RequestCache::remember($cacheKey, function () use ($planPeriodId, $schoolId, $commodityId) {
            $stmt = StatementCache::prepare($this->db, 'SELECT planned_quantity, remaining_quantity FROM supply_plans WHERE plan_period_id = :plan_period_id AND school_id = :school_id AND commodity_id = :commodity_id LIMIT 1');
            $stmt->execute([
                'plan_period_id' => $planPeriodId,
                'school_id' => $schoolId,
                'commodity_id' => $commodityId,
            ]);
            $row = $stmt->fetch();
            if (!$row) {
                return null;
            }
            if ($row['remaining_quantity'] === null) {
                $row['remaining_quantity'] = $row['planned_quantity'];
            }
            return $row;
        });
    }

    public function getPlanRowForUpdate(int $planPeriodId, int $schoolId, int $commodityId): ?array
    {
        $stmt = StatementCache::prepare($this->db, 'SELECT * FROM supply_plans WHERE plan_period_id = :plan_period_id AND school_id = :school_id AND commodity_id = :commodity_id LIMIT 1 FOR UPDATE');
        $stmt->execute([
            'plan_period_id' => $planPeriodId,
            'school_id' => $schoolId,
            'commodity_id' => $commodityId,
        ]);
        return $stmt->fetch() ?: null;
    }

    public function list(array $filters, int $page = 1, int $perPage = 20): array
    {
        $conditions = [];
        $params = [];
        foreach (['plan_period_id', 'school_id', 'commodity_id'] as $field) {
            if (!empty($filters[$field])) {
                $conditions[] = "sp.$field = :$field";
                $params[$field] = $filters[$field];
            }
        }
        $where = $conditions ? 'WHERE ' . implode(' AND ', $conditions) : '';
        $offset = ($page - 1) * $perPage;

        $sql = "SELECT sp.*, schools.name as school_name, commodities.name as commodity_name FROM supply_plans sp
                JOIN schools ON schools.id = sp.school_id
                JOIN commodities ON commodities.id = sp.commodity_id
                $where ORDER BY schools.name LIMIT :limit OFFSET :offset";
        $stmt = $this->db->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue(':' . $key, $value);
        }
        $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        $items = $stmt->fetchAll();

        $countStmt = $this->db->prepare('SELECT COUNT(*) FROM supply_plans sp ' . $where);
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

    public function bulkUpsert(array $rows): int
    {
        if (empty($rows)) {
            return 0;
        }
        $placeholders = [];
        $params = [];
        $now = date('Y-m-d H:i:s');
        foreach ($rows as $index => $row) {
            $placeholders[] = '(?,?,?,?,?,?,?)';
            $params[] = $row['plan_period_id'];
            $params[] = $row['school_id'];
            $params[] = $row['commodity_id'];
            $params[] = $row['planned_quantity'];
            $params[] = $row['planned_quantity'];
            $params[] = $row['notes'];
            $params[] = $now;
        }
        $sql = 'INSERT INTO supply_plans (plan_period_id, school_id, commodity_id, planned_quantity, remaining_quantity, notes, updated_at)
                VALUES ' . implode(',', $placeholders) . '
                ON DUPLICATE KEY UPDATE
                    planned_quantity = VALUES(planned_quantity),
                    remaining_quantity = LEAST(VALUES(planned_quantity), GREATEST(0, COALESCE(supply_plans.remaining_quantity, supply_plans.planned_quantity, VALUES(planned_quantity)) + (VALUES(planned_quantity) - supply_plans.planned_quantity))),
                    notes = VALUES(notes),
                    updated_at = VALUES(updated_at)';
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->rowCount();
    }
}
