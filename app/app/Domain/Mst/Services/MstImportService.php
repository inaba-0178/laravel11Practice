<?php

declare(strict_types=1);

namespace App\Domain\Mst\Services;

use App\Constants\MstImportOrder;
use App\Constants\MstTableMap;
use App\Domain\Mst\DataTransformers\MstRowTransformer;
use App\Infrastructure\Eloquent\Mst\MstVersion;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class MstImportService
{
    /**
     * データ投入
     */
    public function import(array $sheets, string $version, string $description, ?callable $onProgress = null): MstVersion
    {
        return DB::connection('mst')->transaction(function () use ($sheets, $version, $description, $onProgress) {

            // バージョンレコード作成
            $mstVersion = MstVersion::create([
                'version'     => $version,
                'description' => $description,
                'status'      => 'draft',
                'uploaded_by' => Auth::id(),
                'uploaded_at' => now(),
            ]);

            // 外部キー制約を一時無効
            DB::connection('mst')->statement('SET FOREIGN_KEY_CHECKS=0');

            try {
                // 投入順序に従ってシートを並び替え
                $orderedSheets = [];
                foreach (MstImportOrder::ORDER as $tableName) {
                    $sheetName = MstTableMap::getSheetName($tableName);
                    if ($sheetName && isset($sheets[$sheetName])) {
                        $orderedSheets[$sheetName] = $sheets[$sheetName];
                    }
                }
                // IMPORT_ORDERに含まれないシートも追加
                foreach ($sheets as $sheetName => $rows) {
                    if (!isset($orderedSheets[$sheetName])) {
                        $orderedSheets[$sheetName] = $rows;
                    }
                }

                $current = 0;
                foreach ($orderedSheets as $sheetName => $rows) {
                    $tableName = MstTableMap::getTableName($sheetName);
                    if (!$tableName) continue;

                    $mappedRows     = MstRowTransformer::mapHeaders($rows, $tableName);
                    $normalizedRows = MstRowTransformer::normalizeBoolean($mappedRows);
                    $numberedRows   = MstRowTransformer::normalizeNumbers($normalizedRows);
                    $resolvedRows   = MstRowTransformer::resolveSeriesIds($numberedRows, $tableName);
                    $finalRows      = MstRowTransformer::resolveVehicleIds($resolvedRows, $tableName);

                    $current++;
                    if ($onProgress) {
                        $onProgress($sheetName, $current);
                    }

                    DB::connection('mst')->table($tableName)->delete();

                    $insertData = [];
                    foreach ($finalRows as $row) {
                        $row['version_id'] = $mstVersion->id;
                        $row['created_at'] = now();
                        $row['updated_at'] = now();
                        $insertData[]      = $row;
                    }

                    foreach (array_chunk($insertData, 1000) as $chunk) {
                        DB::connection('mst')->table($tableName)->insert($chunk);
                    }
                }
            } finally {
                DB::connection('mst')->statement('SET FOREIGN_KEY_CHECKS=1');
            }

            return $mstVersion;
        });
    }

    /**
     * テーブルごとの件数を取得（プレビュー用）
     */
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