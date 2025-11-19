<?php

namespace App\Services\Performance;

use PDO;
use PDOStatement;

class StatementCache
{
    /** @var array<string, PDOStatement> */
    private static array $cache = [];

    public static function prepare(PDO $connection, string $sql): PDOStatement
    {
        $key = spl_object_id($connection) . ':' . md5($sql);
        if (!isset(self::$cache[$key])) {
            self::$cache[$key] = $connection->prepare($sql);
        } else {
            self::$cache[$key]->closeCursor();
        }
        return self::$cache[$key];
    }
}
