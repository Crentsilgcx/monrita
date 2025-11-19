<?php

namespace App\Services;

use App\Repositories\LookupRepository;
use App\Repositories\PlanImportRepository;
use App\Repositories\SupplyPlanRepository;
use App\Services\Ingestion\SelfAdaptivePlanIngestor;
use App\Services\PlanPeriodMetricsService;
use RuntimeException;

class PlanImportService
{
    private SupplyPlanRepository $supplyPlanRepository;
    private PlanImportRepository $importRepository;
    private LookupRepository $lookupRepository;
    private SelfAdaptivePlanIngestor $ingestor;
    private PlanPeriodMetricsService $metricsService;

    public function __construct()
    {
        $this->supplyPlanRepository = new SupplyPlanRepository();
        $this->importRepository = new PlanImportRepository();
        $this->lookupRepository = new LookupRepository();
        $this->ingestor = new SelfAdaptivePlanIngestor();
        $this->metricsService = new PlanPeriodMetricsService();
    }

    public function importFile(int $planPeriodId, int $userId, array $file): array
    {
        if (($file['size'] ?? 0) > 10 * 1024 * 1024) {
            throw new RuntimeException('Import payload exceeds 10MB limit');
        }
        $analysis = $this->ingestor->analyze($file);
        $rawPreview = $this->ingestor->preview($file['tmp_name']);
        $importId = $this->importRepository->create([
            'plan_period_id' => $planPeriodId,
            'imported_by' => $userId,
            'original_filename' => $file['name'] ?? 'upload.json',
            'file_type' => $analysis['format'],
            'raw_payload' => $rawPreview,
        ]);

        try {
            $ingested = $this->ingestor->ingest($file, $analysis);
            $rows = $ingested['rows'];
            if (!empty($ingested['headers'])) {
                $this->importRepository->attachHeaders($importId, $ingested['headers']);
            }
            if (empty($rows)) {
                throw new RuntimeException('No rows found in supplied payload');
            }
            $schools = $this->lookupRepository->schoolsByCode();
            $commodities = $this->lookupRepository->commoditiesByCode();
            $chunkSize = $ingested['chunk_size'];
            $imported = 0;
            $skipped = 0;
            $seen = [];

            $normalizedRows = [];
            foreach ($rows as $row) {
                if (!isset($row['school_code'], $row['commodity_code'], $row['planned_quantity'])) {
                    $skipped++;
                    continue;
                }
                $schoolCode = trim((string) $row['school_code']);
                $commodityCode = trim((string) $row['commodity_code']);
                if (!isset($schools[$schoolCode], $commodities[$commodityCode])) {
                    $skipped++;
                    continue;
                }
                $key = $planPeriodId . ':' . $schools[$schoolCode] . ':' . $commodities[$commodityCode];
                if (isset($seen[$key])) {
                    $skipped++;
                    continue;
                }
                $seen[$key] = true;
                $quantity = (float) $row['planned_quantity'];
                if ($quantity < 0) {
                    $skipped++;
                    continue;
                }
                $normalizedRows[] = [
                    'plan_period_id' => $planPeriodId,
                    'school_id' => $schools[$schoolCode],
                    'commodity_id' => $commodities[$commodityCode],
                    'planned_quantity' => $quantity,
                    'notes' => $row['notes'] ?? null,
                ];
            }

            foreach (array_chunk($normalizedRows, $chunkSize) as $chunk) {
                $this->supplyPlanRepository->bulkUpsert($chunk);
                $imported += count($chunk);
            }

            $this->metricsService->syncFromPlans($planPeriodId);

            $this->importRepository->markSuccess($importId, $imported);

            return [
                'import_id' => $importId,
                'imported_rows' => $imported,
                'skipped_rows' => $skipped,
                'format' => $analysis['format'],
                'chunk_size' => $chunkSize,
                'headers' => $ingested['headers'] ?? [],
            ];
        } catch (\Throwable $e) {
            $this->importRepository->markFailed($importId, $e->getMessage());
            throw $e;
        }
    }
}
