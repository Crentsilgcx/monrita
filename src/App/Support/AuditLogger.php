<?php

declare(strict_types=1);

namespace App\Support;

final class AuditLogger
{
    private string $file;

    public function __construct(string $file)
    {
        $this->file = $file;
        $dir = dirname($file);
        if (!is_dir($dir)) {
            mkdir($dir, 0775, true);
        }
    }

    /**
     * @param array<string, mixed> $meta
     */
    public function log(?int $userId, string $action, string $entityType, ?string $entityId, string $ip, string $ua, array $meta = []): void
    {
        $record = [
            'timestamp' => (new \DateTimeImmutable())->format(DATE_ATOM),
            'user_id' => $userId,
            'action' => $action,
            'entity_type' => $entityType,
            'entity_id' => $entityId,
            'ip' => $ip,
            'ua' => $ua,
            'meta' => $meta,
        ];

        file_put_contents($this->file, json_encode($record, JSON_UNESCAPED_UNICODE) . PHP_EOL, FILE_APPEND);
    }
}
