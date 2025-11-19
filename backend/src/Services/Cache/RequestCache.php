<?php

namespace App\Services\Cache;

class RequestCache
{
    private static array $store = [];

    public static function remember(string $key, callable $callback): mixed
    {
        if (!array_key_exists($key, self::$store)) {
            self::$store[$key] = $callback();
        }
        return self::$store[$key];
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        return self::$store[$key] ?? $default;
    }

    public static function put(string $key, mixed $value): void
    {
        self::$store[$key] = $value;
    }
}
