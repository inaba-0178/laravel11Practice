<?php

declare(strict_types=1);

namespace App\Constants;

final class MstTableMap
{
    // シート名（和名） => テーブル名
    public const TABLES = [
        'エリア'                  => 'mst_areas',
        '基本オプション'           => 'mst_basic_options',
        'ボディタイプ画像'         => 'mst_body_type_images',
        'ボディタイプ'             => 'mst_body_types',
        '車種シリーズ'             => 'mst_car_series',
        '車種シリーズボディタイプ'  => 'mst_car_series_body_types',
        '車タイプオプション'       => 'mst_car_type_options',
        'カラーオプション'         => 'mst_color_options',
        '詳細オプション'           => 'mst_detail_options',
        '排気量リスト'             => 'mst_displacement_lists',
        '基本装備'                 => 'mst_equipment_basic',
        'ドレスアップ装備'          => 'mst_equipment_dressup',
        '環境装備'                 => 'mst_equipment_env',
        '安全装備'                 => 'mst_equipment_safety',
        '注目ボディタイプ'          => 'mst_featured_body_types',
        '注目ブランド'             => 'mst_featured_brands',
        '自賠責保険'               => 'mst_liability_insurances',
        'ローンプラン'             => 'mst_loan_plans',
        'メーカー画像'             => 'mst_manufacturer_images',
        'メーカー'                => 'mst_manufacturers',
        '走行距離リスト'           => 'mst_mileage_lists',
        '価格リスト'              => 'mst_price_lists',
        '都道府県'                => 'mst_regions',
        '乗車定員リスト'          => 'mst_riding_capacity_lists',
        'シートオプション'        => 'mst_seat_options',
        '自動車重量税'            => 'mst_vehicle_weight_taxes',
        '車両年式バージョン'       => 'mst_vehicle_year_versions',
        '車両'                    => 'mst_vehicles',
        '車種シリーズ画像'          => 'mst_car_series_images',
    ];

    // 画像を持つテーブルとS3フォルダの対応
    public const IMAGE_FOLDERS = [
        'mst_body_type_images'      => 'mst/body_types/',
        'mst_manufacturer_images'   => 'mst/manufacturers/',
        'mst_car_series_images'     => 'mst/car_series/',
        // 'mst_featured_body_types'   => 'mst/featured_body_types/',
        // 'mst_featured_brands'       => 'mst/featured_brands/',
    ];

    public static function getTableName(string $sheetName): ?string
    {
        return self::TABLES[$sheetName] ?? null;
    }

    public static function getImageFolder(string $tableName): ?string
    {
        return self::IMAGE_FOLDERS[$tableName] ?? null;
    }

    public static function hasImage(string $tableName): bool
    {
        return isset(self::IMAGE_FOLDERS[$tableName]);
    }

    public static function getSheetName(string $tableName): ?string
    {
        $flipped = array_flip(self::TABLES);
        return $flipped[$tableName] ?? null;
    }
}