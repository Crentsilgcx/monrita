<?php
return [
    'db' => [
        'host' => getenv('DB_HOST') ?: '127.0.0.1',
        'port' => getenv('DB_PORT') ?: '3306',
        'database' => getenv('DB_NAME') ?: 'monrita',
        'username' => getenv('DB_USER') ?: 'root',
        'password' => getenv('DB_PASS') ?: '',
        'charset' => 'utf8mb4',
    ],
    'security' => [
        'jwt_secret' => getenv('JWT_SECRET') ?: 'changeme-secret-key',
        'token_ttl_minutes' => 720,
    ],
    'app' => [
        'debug' => (bool) getenv('APP_DEBUG'),
        'timezone' => getenv('APP_TIMEZONE') ?: 'UTC',
        'cache_ttl' => 60,
    ],
];
