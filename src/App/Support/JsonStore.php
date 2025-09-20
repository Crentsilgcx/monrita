<?php

declare(strict_types=1);

namespace App\Support;

use RuntimeException;

final class JsonStore
{
    private string $directory;

    public function __construct(string $directory)
    {
        $this->directory = rtrim($directory, '/');
        if (!is_dir($this->directory) && !mkdir($this->directory, 0775, true) && !is_dir($this->directory)) {
            throw new RuntimeException('Unable to create data directory: ' . $this->directory);
        }
    }

    /**
     * @return array<int|string, mixed>
     */
    public function read(string $name): array
    {
        $file = $this->path($name);
        if (!is_file($file)) {
            return [];
        }

        $json = file_get_contents($file);
        if ($json === false) {
            throw new RuntimeException('Unable to read data file: ' . $file);
        }

        $data = json_decode($json, true, 512, JSON_THROW_ON_ERROR);

        return is_array($data) ? $data : [];
    }

    /**
     * @param array<int|string, mixed> $data
     */
    public function write(string $name, array $data): void
    {
        $file = $this->path($name);
        $json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        if ($json === false) {
            throw new RuntimeException('Unable to encode json for ' . $name);
        }

        if (file_put_contents($file, $json) === false) {
            throw new RuntimeException('Unable to write data file: ' . $file);
        }
    }

    private function path(string $name): string
    {
        return $this->directory . '/' . $name . '.json';
    }
}
