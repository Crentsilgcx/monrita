<?php

namespace App\Services\Ingestion;

use RuntimeException;

class SelfAdaptivePlanIngestor
{
    private XlsxStreamReader $xlsxReader;

    /** @var array<string, string[]> */
    private array $fieldAliases = [
        'school_code' => ['school_code', 'school', 'school id', 'school_code', 'campus_code', 'code'],
        'commodity_code' => ['commodity_code', 'commodity', 'item', 'product_code', 'item_code'],
        'planned_quantity' => ['planned_quantity', 'planned', 'quantity', 'qty', 'allocation', 'qty_planned'],
        'notes' => ['notes', 'note', 'remarks', 'comment', 'comments'],
    ];

    public function __construct()
    {
        $this->xlsxReader = new XlsxStreamReader();
    }

    public function analyze(array $file): array
    {
        $format = $this->detectFormat($file);
        $size = (int) ($file['size'] ?? (is_file($file['tmp_name'] ?? '') ? filesize($file['tmp_name']) : 0));
        return [
            'format' => $format,
            'size' => $size,
            'chunk_size' => $this->determineChunkSize($size),
            'headers' => [],
        ];
    }

    public function ingest(array $file, ?array $analysis = null): array
    {
        $analysis = $analysis ?? $this->analyze($file);
        $path = $file['tmp_name'] ?? null;
        if (!$path || !is_file($path)) {
            throw new RuntimeException('Uploaded file cannot be accessed');
        }

        $rows = match ($analysis['format']) {
            'JSON' => $this->parseJson($path),
            'CSV' => $this->parseCsv($path),
            'XLSX' => $this->parseXlsx($path),
            default => throw new RuntimeException('Unsupported plan import format'),
        };

        if (empty($rows)) {
            throw new RuntimeException('File does not contain any data rows');
        }

        $normalized = [];
        foreach ($rows as $row) {
            $canonical = $this->normalizeRow($row);
            if ($canonical) {
                $normalized[] = $canonical;
            }
        }

        if (empty($normalized)) {
            throw new RuntimeException('No valid plan rows were detected after normalization');
        }

        $analysis['headers'] = array_keys($rows[0]);

        return [
            'format' => $analysis['format'],
            'chunk_size' => $analysis['chunk_size'],
            'rows' => $normalized,
            'headers' => $analysis['headers'],
        ];
    }

    public function preview(string $path): string
    {
        $handle = fopen($path, 'rb');
        if (!$handle) {
            return '';
        }
        $preview = stream_get_contents($handle, 12000) ?: '';
        fclose($handle);
        return $preview;
    }

    private function detectFormat(array $file): string
    {
        $name = strtolower((string) ($file['name'] ?? ''));
        $type = strtolower((string) ($file['type'] ?? ''));
        if (str_ends_with($name, '.json') || $type === 'application/json') {
            return 'JSON';
        }
        if (str_ends_with($name, '.csv') || str_contains($type, 'csv')) {
            return 'CSV';
        }
        if (str_ends_with($name, '.xlsx') || str_contains($type, 'spreadsheetml')) {
            return 'XLSX';
        }
        throw new RuntimeException('Please upload JSON, CSV, or XLSX files');
    }

    private function parseJson(string $path): array
    {
        $contents = file_get_contents($path);
        if ($contents === false) {
            throw new RuntimeException('Unable to read uploaded JSON');
        }
        $decoded = json_decode($contents, true);
        if (!is_array($decoded)) {
            throw new RuntimeException('Invalid JSON structure');
        }
        if (isset($decoded['rows']) && is_array($decoded['rows'])) {
            $decoded = $decoded['rows'];
        }
        if (isset($decoded['data']) && is_array($decoded['data'])) {
            $decoded = $decoded['data'];
        }
        return array_values(array_filter($decoded, 'is_array'));
    }

    private function parseCsv(string $path): array
    {
        $file = new \SplFileObject($path);
        $file->setFlags(\SplFileObject::READ_CSV | \SplFileObject::SKIP_EMPTY | \SplFileObject::DROP_NEW_LINE);
        $headers = [];
        $rows = [];
        foreach ($file as $index => $row) {
            if ($row === [null] || $row === false) {
                continue;
            }
            if ($index === 0) {
                $headers = array_map('trim', $row);
                continue;
            }
            if (empty($headers)) {
                continue;
            }
            $assoc = [];
            foreach ($headers as $position => $header) {
                $assoc[$header ?: 'column_' . $position] = $row[$position] ?? null;
            }
            $rows[] = $assoc;
        }
        return $rows;
    }

    private function parseXlsx(string $path): array
    {
        $rows = $this->xlsxReader->rows($path);
        if (empty($rows)) {
            return [];
        }
        $headers = array_shift($rows);
        $assoc = [];
        foreach ($rows as $row) {
            $item = [];
            foreach ($headers as $index => $header) {
                $item[$header ?: 'column_' . $index] = $row[$index] ?? null;
            }
            $assoc[] = $item;
        }
        return $assoc;
    }

    private function normalizeRow(array $row): ?array
    {
        $normalized = [];
        $flattened = [];
        foreach ($row as $key => $value) {
            $flattened[$this->normalizeKey((string) $key)] = is_string($value) ? trim($value) : $value;
        }
        foreach ($this->fieldAliases as $target => $aliases) {
            foreach ($aliases as $alias) {
                $aliasKey = $this->normalizeKey($alias);
                if (array_key_exists($aliasKey, $flattened)) {
                    $normalized[$target] = $flattened[$aliasKey];
                    break;
                }
            }
        }
        if (!isset($normalized['school_code'], $normalized['commodity_code'], $normalized['planned_quantity'])) {
            return null;
        }
        $normalized['school_code'] = strtoupper(trim((string) $normalized['school_code']));
        $normalized['commodity_code'] = strtoupper(trim((string) $normalized['commodity_code']));
        $normalized['planned_quantity'] = (float) $normalized['planned_quantity'];
        $normalized['notes'] = $normalized['notes'] ?? null;
        return $normalized;
    }

    private function normalizeKey(string $key): string
    {
        $key = strtolower($key);
        $key = str_replace([' ', '-', '.', '__'], '_', $key);
        return preg_replace('/[^a-z0-9_]/', '', $key) ?: $key;
    }

    private function determineChunkSize(int $size): int
    {
        if ($size <= 512 * 1024) {
            return 600;
        }
        if ($size <= 2 * 1024 * 1024) {
            return 400;
        }
        if ($size <= 5 * 1024 * 1024) {
            return 300;
        }
        return 200;
    }
}
