<?php

namespace App\Repositories;

use App\Config\Database;
use App\Services\Cache\RequestCache;
use PDO;

class LookupRepository
{
    private PDO $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function schoolsByCode(): array
    {
        return RequestCache::remember('schools_by_code', function () {
            $stmt = $this->db->query('SELECT id, code FROM schools WHERE is_active = 1');
            $map = [];
            foreach ($stmt->fetchAll() as $row) {
                $map[$row['code']] = (int) $row['id'];
            }
            return $map;
        });
    }

    public function commoditiesByCode(): array
    {
        return RequestCache::remember('commodities_by_code', function () {
            $stmt = $this->db->query('SELECT id, code FROM commodities WHERE is_active = 1');
            $map = [];
            foreach ($stmt->fetchAll() as $row) {
                $map[$row['code']] = (int) $row['id'];
            }
            return $map;
        });
    }
}
