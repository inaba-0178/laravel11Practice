<?php

declare(strict_types=1);

namespace App\Domain\Mst\DataTransformers;

use App\Constants\MstTableColumns;
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
     * メーカー名 → manufacturer_id に変換
     * カラムが存在して文字列の場合のみ変換する
     */
    public static function resolveManufacturerIds(array $rows, array $manufacturers): array
    {
        if (empty($manufacturers)) return $rows;

        return array_map(function ($row) use ($manufacturers) {
            if (isset($row['manufacturer_id']) && !is_numeric($row['manufacturer_id'])) {
                $name = trim($row['manufacturer_id']);
                if (isset($manufacturers[$name])) {
                    $row['manufacturer_id'] = $manufacturers[$name];
                } else {
                    Log::warning("MstRowTransformer: manufacturer not found: {$name}");
                }
            }
            return $row;
        }, $rows);
    }

    /**
     * ボディタイプ名 → body_type_id に変換
     * カラムが存在して文字列の場合のみ変換する
     */
    public static function resolveBodyTypeIds(array $rows, array $bodyTypes): array
    {
        if (empty($bodyTypes)) return $rows;

        return array_map(function ($row) use ($bodyTypes) {
            // body_type カラム（mst_vehicles）
            if (isset($row['body_type']) && !is_numeric($row['body_type'])) {
                $name = trim($row['body_type']);
                if (isset($bodyTypes[$name])) {
                    $row['body_type'] = $bodyTypes[$name];
                } else {
                    Log::warning("MstRowTransformer: body_type not found: {$name}");
                }
            }
            // body_type_id カラム（mst_car_series_body_types）
            if (isset($row['body_type_id']) && !is_numeric($row['body_type_id'])) {
                $name = trim($row['body_type_id']);
                if (isset($bodyTypes[$name])) {
                    $row['body_type_id'] = $bodyTypes[$name];
                } else {
                    Log::warning("MstRowTransformer: body_type_id not found: {$name}");
                }
            }
            return $row;
        }, $rows);
    }

    /**
     * シリーズ名 → series_id に変換
     * メーカー名がある場合はメーカー名+シリーズ名で特定
     * file_pathがある場合はfile_pathからメーカー名を取得して特定
     * どちらもない場合はシリーズ名のみで検索（同名は最初にマッチしたもの）
     */
    public static function resolveSeriesIds(array $rows, array $manufacturers, array $series, string $tableName = ''): array
    {
        if (empty($series)) return $rows;

        // series_id が string 型のテーブルかチェック
        $columns   = MstTableColumns::getColumns($tableName);
        $seriesCol = collect($columns)->firstWhere('name', 'series_id');
        $isString  = ($seriesCol['type'] ?? '') === 'string';

        return array_map(function ($row) use ($manufacturers, $series, $isString) {
            if (!isset($row['series_id'])) {
                unset($row['manufacturer_name']);
                return $row;
            }

            // string 型でない場合は変換不要
            if (!$isString) {
                unset($row['manufacturer_name']);
                return $row;
            }

            // 既に数値IDの場合はスキップ（ただし string 型テーブルは通す）
            $seriesName = trim((string) $row['series_id']);

            // メーカー名カラムがある場合
            if (isset($row['manufacturer_name']) && !empty($row['manufacturer_name'])) {
                $manufacturerName = trim($row['manufacturer_name']);
                $manufacturerId   = $manufacturers[$manufacturerName] ?? null;

                if (!$manufacturerId) {
                    Log::warning("MstRowTransformer: manufacturer not found: {$manufacturerName}");
                    unset($row['manufacturer_name']);
                    return $row;
                }

                $key = "{$manufacturerId}_{$seriesName}";
                if (isset($series[$key])) {
                    $row['series_id'] = $series[$key];
                } else {
                    Log::warning("MstRowTransformer: series not found: {$manufacturerName} / {$seriesName}");
                }

                unset($row['manufacturer_name']);
                return $row;
            }

            // file_pathがある場合はfile_pathからメーカー名を取得
            if (isset($row['file_path']) && !empty($row['file_path'])) {
                $filePath         = trim($row['file_path']);
                $manufacturerName = explode('/', $filePath)[0] ?? '';
                $manufacturerId   = $manufacturers[$manufacturerName] ?? null;

                if (!$manufacturerId) {
                    Log::warning("MstRowTransformer: manufacturer not found from file_path: {$manufacturerName}");
                } else {
                    $key = "{$manufacturerId}_{$seriesName}";
                    if (isset($series[$key])) {
                        $row['series_id'] = $series[$key];
                    } else {
                        Log::warning("MstRowTransformer: series not found: {$manufacturerName} / {$seriesName}");
                    }
                }
                unset($row['manufacturer_name']);
                return $row;
            }

            // どちらもない場合はシリーズ名のみで検索
            $matchedSeries = collect($series)
                ->filter(fn ($id, $key) => str_ends_with($key, "_{$seriesName}"))
                ->values()
                ->first();

            if ($matchedSeries) {
                $row['series_id'] = $matchedSeries;
            } else {
                Log::warning("MstRowTransformer: series not found: {$seriesName}");
            }

            return $row;
        }, $rows);
    }

    /**
     * 車両名 → vehicle_id に変換
     */
    public static function resolveVehicleIds(array $rows, array $vehicles): array
    {
        if (empty($vehicles)) return $rows;

        return array_map(function ($row) use ($vehicles) {
            if (isset($row['vehicle_id']) && !is_numeric($row['vehicle_id'])) {
                $name = trim($row['vehicle_id']);
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