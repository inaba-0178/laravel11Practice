<?php

declare(strict_types=1);

namespace App\Domain\Mst\DataTransformers;

use App\Constants\MstTableColumns;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MstRowTransformer
{
    /**
     * 日本語ヘッダー → 英語カラム名に変換
     */
    public static function mapHeaders(array $rows, string $tableName): array
    {
        if (empty($rows)) return [];

        $columns   = MstTableColumns::getColumns($tableName);
        $headerMap = collect($columns)->pluck('name', 'label_ja')->toArray();

        return array_map(function ($row) use ($headerMap) {
            $mapped = [];
            foreach ($row as $key => $value) {
                $mappedKey          = $headerMap[$key] ?? $key;
                $mapped[$mappedKey] = $value;
            }
            return $mapped;
        }, $rows);
    }

    /**
     * TRUE/FALSE文字列 → 1/0に変換
     */
    public static function normalizeBoolean(array $rows): array
    {
        return array_map(function ($row) {
            return array_map(function ($value) {
                if ($value === 'TRUE' || $value === 'true') return 1;
                if ($value === 'FALSE' || $value === 'false') return 0;
                return $value;
            }, $row);
        }, $rows);
    }

    /**
     * カンマ区切り数値・null文字列・非公表 → 正規化
     */
    public static function normalizeNumbers(array $rows): array
    {
        return array_map(function ($row) {
            return array_map(function ($value) {
                if ($value === 'null' || $value === 'NULL') return null;
                if ($value === '非公表' || $value === '-' || $value === 'N/A') return null;
                if (is_string($value) && preg_match('/^[\d,]+$/', $value)) {
                    return str_replace(',', '', $value);
                }
                return $value;
            }, $row);
        }, $rows);
    }

    /**
     * シリーズ名（CT等）→ series_id に変換（mst_vehicles投入時）
     */
    public static function resolveSeriesIds(array $rows, string $tableName): array
    {
        if (!in_array($tableName, ['mst_vehicles', 'mst_car_series_images'])) return $rows;

        // mst_car_series_images の場合は file_path のメーカー名 + シリーズ名で特定
        if ($tableName === 'mst_car_series_images') {
            $manufacturers = DB::connection('mst')
                ->table('mst_manufacturers')
                ->pluck('id', 'name')
                ->toArray();

            $series = DB::connection('mst')
                ->table('mst_car_series')
                ->get()
                ->mapWithKeys(fn ($s) => ["{$s->manufacturer_id}_{$s->series_name}" => $s->series_id])
                ->toArray();

            return array_map(function ($row) use ($manufacturers, $series) {
                $seriesName = trim($row['series_id'] ?? '');
                $filePath   = trim($row['file_path'] ?? '');

                if (!empty($filePath)) {
                    // file_path からメーカー名取得して特定
                    // 例: LEXUS/ct.jpg → LEXUS
                    $manufacturerName = explode('/', $filePath)[0] ?? '';
                    $manufacturerId   = $manufacturers[$manufacturerName] ?? null;

                    if (!$manufacturerId) {
                        Log::warning("MstRowTransformer: manufacturer not found: {$manufacturerName}");
                        return $row;
                    }

                    $key = "{$manufacturerId}_{$seriesName}";
                    if (isset($series[$key])) {
                        $row['series_id'] = $series[$key];
                    } else {
                        Log::warning("MstRowTransformer: series not found: {$manufacturerName} / {$seriesName}");
                    }
                } else {
                    // file_path が空の場合はシリーズ名のみで検索
                    // 同名が複数ある場合は最初にマッチしたものを使う
                    $matchedSeries = collect($series)
                        ->filter(fn ($id, $key) => str_ends_with($key, "_{$seriesName}"))
                        ->values()
                        ->first();

                    if ($matchedSeries) {
                        $row['series_id'] = $matchedSeries;
                    } else {
                        Log::warning("MstRowTransformer: series not found: {$seriesName}");
                    }
                }

                return $row;
            }, $rows);
        }

        // mst_vehicles の場合は既存処理
        $series = DB::connection('mst')
            ->table('mst_car_series')
            ->pluck('series_id', 'series_name')
            ->toArray();

        return array_map(function ($row) use ($series) {
            if (isset($row['series_id']) && !is_numeric($row['series_id'])) {
                $name = trim($row['series_id']);
                if (isset($series[$name])) {
                    $row['series_id'] = $series[$name];
                } else {
                    Log::warning("MstRowTransformer: series not found: {$name}");
                }
            }
            return $row;
        }, $rows);
    }

    /**
     * 車両名 → vehicle_id に変換（mst_vehicle_year_versions投入時）
     */
    public static function resolveVehicleIds(array $rows, string $tableName): array
    {
        if ($tableName !== 'mst_vehicle_year_versions') return $rows;

        $vehicles = DB::connection('mst')
            ->table('mst_vehicles')
            ->pluck('id', 'name')
            ->toArray();

        return array_map(function ($row) use ($vehicles) {
            if (isset($row['vehicle_id']) && !is_numeric($row['vehicle_id'])) {
                $name = $row['vehicle_id'];
                if (isset($vehicles[$name])) {
                    $row['vehicle_id'] = $vehicles[$name];
                } else {
                    Log::warning("MstRowTransformer: vehicle not found: {$name}");
                }
            }
            return $row;
        }, $rows);
    }
}