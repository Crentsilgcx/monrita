<?php

declare(strict_types=1);

namespace App\Support;

final class Cache
{
    private string $path;

    public function __construct(string $path)
    {
        $this->path = rtrim($path, '/');
        if (!is_dir($this->path)) {
            mkdir($this->path, 0775, true);
        }
    }

    public function remember(string $key, int $seconds, callable $resolver)
    {
        $file = $this->filePath($key);
        if (is_file($file)) {
            $payload = unserialize((string) file_get_contents($file), ['allowed_classes' => false]);
            if (is_array($payload) && ($payload['expires_at'] ?? 0) >= time()) {
                return $payload['value'] ?? null;
            }
        }

        $value = $resolver();
        $payload = [
            'expires_at' => time() + $seconds,
            'value' => $value,
        ];
        file_put_contents($file, serialize($payload));

        return $value;
    }

    public function forgetByPrefix(string $prefix): void
    {
        foreach (glob($this->path . '/' . md5($prefix) . '*') as $file) {
            if (is_file($file)) {
                unlink($file);
            }
        }
    }

    private function filePath(string $key): string
    {
        return $this->path . '/' . md5($key);
    }
}
