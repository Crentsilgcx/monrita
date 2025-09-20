<?php

declare(strict_types=1);

namespace App\Support;

final class View
{
    public function __construct(
        private string $basePath,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    public function render(string $template, array $data = []): string
    {
        $file = $this->basePath . '/' . $template . '.php';
        if (!is_file($file)) {
            throw new \RuntimeException('View not found: ' . $file);
        }

        extract($data, EXTR_SKIP);
        ob_start();
        include $file;

        return (string) ob_get_clean();
    }
}
