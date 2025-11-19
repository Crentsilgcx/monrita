<?php

namespace App\Repositories;

use App\Config\Database;
use PDO;

class PlanImportRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function create(array $data): int
    {
        $sql = 'INSERT INTO plan_imports (plan_period_id, imported_by, original_filename, file_type, imported_rows, status, error_message, raw_payload, detected_headers)
                VALUES (:plan_period_id, :imported_by, :original_filename, :file_type, :imported_rows, :status, :error_message, :raw_payload, :detected_headers)';
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'plan_period_id' => $data['plan_period_id'],
            'imported_by' => $data['imported_by'],
            'original_filename' => $data['original_filename'],
            'file_type' => $data['file_type'],
            'imported_rows' => $data['imported_rows'] ?? 0,
            'status' => $data['status'] ?? 'PENDING',
            'error_message' => $data['error_message'] ?? null,
            'raw_payload' => $data['raw_payload'] ?? null,
            'detected_headers' => $data['detected_headers'] ?? null,
        ]);
        return (int) $this->db->lastInsertId();
    }

    public function markSuccess(int $id, int $rows): void
    {
        $stmt = $this->db->prepare('UPDATE plan_imports SET imported_rows = :rows, status = "SUCCESS" WHERE id = :id');
        $stmt->execute(['rows' => $rows, 'id' => $id]);
    }

    public function markFailed(int $id, string $message): void
    {
        $stmt = $this->db->prepare('UPDATE plan_imports SET status = "FAILED", error_message = :message WHERE id = :id');
        $stmt->execute(['message' => $message, 'id' => $id]);
    }

    public function list(int $page, int $perPage): array
    {
        $offset = ($page - 1) * $perPage;
        $sql = 'SELECT pi.*, pp.label as plan_period_label, u.name as imported_by_name FROM plan_imports pi
                JOIN plan_periods pp ON pp.id = pi.plan_period_id
                JOIN users u ON u.id = pi.imported_by
                ORDER BY pi.created_at DESC LIMIT :limit OFFSET :offset';
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        $items = $stmt->fetchAll();
        $count = (int) $this->db->query('SELECT COUNT(*) FROM plan_imports')->fetchColumn();
        return [
            'data' => $items,
            'pagination' => [
                'page' => $page,
                'per_page' => $perPage,
                'total' => $count,
            ],
        ];
    }

    public function attachHeaders(int $id, array $headers): void
    {
        $stmt = $this->db->prepare('UPDATE plan_imports SET detected_headers = :headers WHERE id = :id');
        $stmt->execute([
            'headers' => json_encode(array_values(array_unique($headers))),
            'id' => $id,
        ]);
    }
}
