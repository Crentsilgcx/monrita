<?php

namespace App\Config;

use PDO;
use PDOException;

class Database
{
    private static ?PDO $connection = null;

    public static function getConnection(): PDO
    {
        if (self::$connection instanceof PDO) {
            return self::$connection;
        }

        $config = require __DIR__ . '/config.php';
        $db = $config['db'];
        $dsn = sprintf('mysql:host=%s;port=%s;dbname=%s;charset=%s', $db['host'], $db['port'], $db['database'], $db['charset']);

        try {
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::ATTR_PERSISTENT => true,
                PDO::ATTR_STRINGIFY_FETCHES => false,
                PDO::ATTR_TIMEOUT => 5,
            ];
            if (defined('PDO::MYSQL_ATTR_USE_BUFFERED_QUERY')) {
                $options[PDO::MYSQL_ATTR_USE_BUFFERED_QUERY] = true;
            }
            if (defined('PDO::MYSQL_ATTR_INIT_COMMAND')) {
                $options[PDO::MYSQL_ATTR_INIT_COMMAND] = "SET NAMES {$db['charset']} COLLATE {$db['charset']}_general_ci";
            }

            self::$connection = new PDO($dsn, $db['username'], $db['password'], $options);
            self::$connection->exec('SET SESSION innodb_strict_mode=ON');
            return self::$connection;
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Database connection failed', 'message' => $e->getMessage()]);
            exit;
        }
    }
}
