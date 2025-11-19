<?php

namespace App\Utils;

class Pagination
{
    public static function sanitize(int $page, int $perPage, int $maxPerPage = 200): array
    {
        $page = max($page, 1);
        $perPage = max(1, min($perPage, $maxPerPage));
        return [$page, $perPage];
    }
}
