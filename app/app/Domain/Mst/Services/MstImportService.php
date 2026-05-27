<?php

declare(strict_types=1);

namespace App\Domain\Mst\Services;

use App\Constants\MstImportOrder;
use App\Constants\MstTableMap;
use App\Domain\Mst\DataTransformers\MstRowTransformer;
use App\Infrastructure\Eloquent\Mst\MstVersion;
use App\Infrastructure\Eloquent\Mst\MstManufacturers;
use App\Infrastructure\Eloquent\Mst\MstCarSeries;
use App\Infrastructure\Eloquent\Mst\MstBodyTypes;
use App\Infrastructure\Eloquent\Mst\MstVehicles;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class MstImportService
{
    public function import(array $sheets, string $version, string $description, ?callable $onProgress = null): MstVersion
    {
        return DB::connection('mst')->transaction(function () use ($sheets, $version, $description, $onProgress) {

            MstVersion::where('status', 'active')->update(['status' => 'archived']);

            $mstVersion = MstVersion::create([
                'version'      => $version,
                'description'  => $description,
                'status'       => 'active',
                'uploaded_by'  => Auth::id(),
                'uploaded_at'  => now(),
                'activated_by' => Auth::id(),
                'activated_at' => now(),
            ]);

            DB::connection('mst')->statement('SET FOREIGN_KEY_CHECKS=0');

            try {
                // マスターデータ取得（変換用）
                $manufacturers = MstManufacturers::query()->pluck('id', 'name')->toArray();
                $bodyTypes     = MstBodyTypes::query()->pluck('id', 'name')->toArray();
                $vehicles      = MstVehicles::query()->pluck('id', 'name')->toArray();
                $series        = MstCarSeries::query()
                    ->get()
                    ->mapWithKeys(fn ($s) => ["{$s->manufacturer_id}_{$s->series_name}" => $s->series_id])
                    ->toArray();

                $orderedSheets = [];
                foreach (MstImportOrder::ORDER as $tableName) {
                    $sheetName = MstTableMap::getSheetName($tableName);
                    if ($sheetName && isset($sheets[$sheetName])) {
                        $orderedSheets[$sheetName] = $sheets[$sheetName];
                    }
                }
                foreach ($sheets as $sheetName => $rows) {
                    if (!isset($orderedSheets[$sheetName])) {
                        $orderedSheets[$sheetName] = $rows;
                    }
                }

                $current = 0;
                foreach ($orderedSheets as $sheetName => $rows) {
                    $tableName = MstTableMap::getTableName($sheetName);
                    if (!$tableName) continue;

                    // 全マスターデータを都度取得
                    $manufacturers = MstManufacturers::query()->pluck('id', 'name')->toArray();
                    $bodyTypes     = MstBodyTypes::query()->pluck('id', 'name')->toArray();

                    $series   = MstCarSeries::query()
                        ->get()
                        ->mapWithKeys(fn ($s) => ["{$s->manufacturer_id}_{$s->series_name}" => $s->series_id])
                        ->toArray();
                    $vehicles = MstVehicles::query()->pluck('id', 'name')->toArray();
                    
                    $mappedRows     = MstRowTransformer::mapHeaders($rows, $tableName);
                    $normalizedRows = MstRowTransformer::normalizeBoolean($mappedRows);
                    $numberedRows   = MstRowTransformer::normalizeNumbers($normalizedRows);
                    $resolvedRows   = MstRowTransformer::resolveManufacturerIds($numberedRows, $manufacturers);
                    $resolvedRows   = MstRowTransformer::resolveBodyTypeIds($resolvedRows, $bodyTypes);
                    $resolvedRows = MstRowTransformer::resolveSeriesIds($resolvedRows, $manufacturers, $series, $tableName);
                    $finalRows      = MstRowTransformer::resolveVehicleIds($resolvedRows, $vehicles);

                    $current++;
                    if ($onProgress) {
                        $onProgress($sheetName, $current);
                    }

                    $this->backupTable($tableName, $mstVersion->id);

                    DB::connection('mst')->table($tableName)->delete();

                    $insertData = [];
                    foreach ($finalRows as $row) {
                        $row['version_id'] = $mstVersion->id;
                        $row['created_at'] = now();
                        $row['updated_at'] = now();
                        $insertData[]      = $row;
                    }

                    foreach (array_chunk($insertData, 1000) as $chunk) {
                          \Log::info('insert data sample', ['tableName' => $tableName, 'row' => $insertData[33] ?? $insertData[0] ?? []]);
                        DB::connection('mst')->table($tableName)->insert($chunk);
                    }
                }
            } finally {
                DB::connection('mst')->statement('SET FOREIGN_KEY_CHECKS=1');
            }

            return $mstVersion;
        });
    }

    private function backupTable(string $tableName, int $newVersionId): void
    {
        $backupTable = $tableName . '_backups';

        $rows = DB::connection('mst')->table($tableName)->get()->toArray();

        if (empty($rows)) return;

        $insertData = array_map(fn ($row) => (array) $row, $rows);

        $existingVersionId = $insertData[0]['version_id'] ?? null;
        if ($existingVersionId) {
            DB::connection('mst_backup')
                ->table($backupTable)
                ->where('version_id', $existingVersionId)
                ->delete();
        }

        foreach (array_chunk($insertData, 1000) as $chunk) {
            DB::connection('mst_backup')->table($backupTable)->insert($chunk);
        }
    }

    public function getPreviewCounts(array $sheets): array
    {
        $counts = [];
        foreach ($sheets as $sheetName => $rows) {
            $tableName          = MstTableMap::getTableName($sheetName);
            $currentCount       = DB::connection('mst')->table($tableName)->count();
            $counts[$sheetName] = [
                'table'         => $tableName,
                'current_count' => $currentCount,
                'new_count'     => count($rows),
            ];
        }
        return $counts;
    }
}