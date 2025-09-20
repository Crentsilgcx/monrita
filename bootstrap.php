<?php

declare(strict_types=1);

spl_autoload_register(static function (string $class): void {
    $prefix = 'App\\';
    if (str_starts_with($class, $prefix)) {
        $relative = substr($class, strlen($prefix));
        $path = __DIR__ . '/src/App/' . str_replace('\\', '/', $relative) . '.php';
        if (is_file($path)) {
            require_once $path;
        }
    }
});

$config = require __DIR__ . '/config/app.php';

date_default_timezone_set($config['timezone']);

return $config;
