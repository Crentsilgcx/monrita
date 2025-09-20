<?php

declare(strict_types=1);

namespace App\Support;

final class SessionManager
{
    public function start(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start([
                'cookie_httponly' => true,
                'cookie_secure' => isset($_SERVER['HTTPS']),
                'cookie_samesite' => 'Lax',
                'use_strict_mode' => true,
            ]);
        }
    }

    public function regenerate(): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_regenerate_id(true);
        }
    }

    public function get(string $key, mixed $default = null): mixed
    {
        return $_SESSION[$key] ?? $default;
    }

    public function put(string $key, mixed $value): void
    {
        $_SESSION[$key] = $value;
    }

    public function forget(string $key): void
    {
        unset($_SESSION[$key]);
    }

    public function flush(): void
    {
        $_SESSION = [];
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_destroy();
        }
    }
}
