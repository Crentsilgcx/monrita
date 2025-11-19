<?php
require_once __DIR__ . '/../../vendor_autoload.php';

spl_autoload_register(function ($class) {
    $prefix = 'App\\';
    $baseDir = __DIR__ . '/../../src/';
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }
    $relativeClass = substr($class, $len);
    $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';
    if (file_exists($file)) {
        require $file;
    }
});

use App\Config\Database;

$pdo = Database::getConnection();

$pdo->exec("INSERT IGNORE INTO users (name, email, password, role, active) VALUES
 ('Super Admin', 'admin@example.com', '" . password_hash('password', PASSWORD_BCRYPT) . "', 'SUPER_ADMIN', 1),
 ('Field Staff', 'field@example.com', '" . password_hash('password', PASSWORD_BCRYPT) . "', 'FIELD_STAFF', 1)");

echo "Seeded users\n";
