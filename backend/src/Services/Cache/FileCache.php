<?php

namespace App\Services\Cache;

use RuntimeException;

class FileCache
{
    private const CACHE_DIR = __DIR__ . '/../../../storage/cache';

    private static function ensureDirectory(): void
    {
        if (!is_dir(self::CACHE_DIR) && !mkdir(self::CACHE_DIR, 0775, true) && !is_dir(self::CACHE_DIR)) {
            throw new RuntimeException('Unable to bootstrap cache directory');
        }
    }

    private static function pathFor(string $key): string
    {
        return self::CACHE_DIR . '/' . sha1($key) . '.cache';
    }

    public static function remember(string $key, int $ttl, callable $callback)
    {
        self::ensureDirectory();
        $path = self::pathFor($key);
        if (file_exists($path)) {
            $payload = json_decode((string) file_get_contents($path), true);
            if ($payload && isset($payload['expires_at']) && $payload['expires_at'] >= time()) {
                return $payload['value'];
            }
        }
        $value = $callback();
        file_put_contents($path, json_encode([
            'value' => $value,
            'expires_at' => time() + $ttl,
        ], JSON_THROW_ON_ERROR));
        return $value;
    }

    public static function put(string $key, $value, int $ttl): void
    {
        self::ensureDirectory();
        $path = self::pathFor($key);
        file_put_contents($path, json_encode([
            'value' => $value,
            'expires_at' => time() + $ttl,
        ], JSON_THROW_ON_ERROR));
    }

    public static function forget(string $key): void
    {
        $path = self::pathFor($key);
        if (file_exists($path)) {
            unlink($path);
        }
    }
}
