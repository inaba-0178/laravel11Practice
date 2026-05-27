<?php

declare(strict_types=1);

namespace App\Constants;

final class MstTableColumns
{
    public const COLUMNS = [
        'mst_areas' => [
            ['name' => 'id',          'label_ja' => 'ID',      'required' => true,  'type' => 'integer'],
            ['name' => 'name',        'label_ja' => '地方名',  'required' => true,  'type' => 'string'],
            ['name' => 'query_param', 'label_ja' => '地方URL', 'required' => true,  'type' => 'string'],
            ['name' => 'sort_order',  'label_ja' => '表示順',  'required' => false, 'type' => 'integer'],
        ],

        'mst_basic_options' => [
            ['name' => 'id',           'label_ja' => 'ID',         'required' => true,  'type' => 'integer'],
            ['name' => 'value',        'label_ja' => '値',          'required' => true,  'type' => 'string'],
            ['name' => 'label',        'label_ja' => '表示名',      'required' => true,  'type' => 'string'],
            ['name' => 'is_highlight', 'label_ja' => 'ハイライト',  'required' => false, 'type' => 'boolean'],
            ['name' => 'sort_order',   'label_ja' => '表示順',      'required' => false, 'type' => 'integer'],
            ['name' => 'is_active',    'label_ja' => '有効フラグ',  'required' => false, 'type' => 'boolean'],
        ],

        'mst_body_type_images' => [
            ['name' => 'id',           'label_ja' => 'ID',             'required' => true,  'type' => 'integer'],
            ['name' => 'body_type_id', 'label_ja' => 'ボディタイプID', 'required' => true,  'type' => 'integer'],
            ['name' => 'image_type',   'label_ja' => '画像種別',       'required' => false, 'type' => 'string'],
            ['name' => 'file_path',    'label_ja' => 'ファイルパス',   'required' => true,  'type' => 'string', 'is_image' => true],
            ['name' => 'alt_text',     'label_ja' => 'ALTテキスト',    'required' => false, 'type' => 'string'],
            ['name' => 'sort_order',   'label_ja' => '表示順',         'required' => false, 'type' => 'integer'],
            ['name' => 'is_main',      'label_ja' => 'メイン画像',     'required' => false, 'type' => 'boolean'],
            ['name' => 'is_active',    'label_ja' => '有効フラグ',     'required' => false, 'type' => 'boolean'],
        ],

        'mst_body_types' => [
            ['name' => 'id',                  'label_ja' => 'ID',       'required' => true,  'type' => 'integer'],
            ['name' => 'name',                'label_ja' => '名称',     'required' => true,  'type' => 'string'],
            ['name' => 'name_kana',           'label_ja' => '名称カナ', 'required' => false, 'type' => 'string'],
            ['name' => 'code',                'label_ja' => 'コード',   'required' => true,  'type' => 'string'],
            ['name' => 'description',         'label_ja' => '説明',     'required' => false, 'type' => 'string'],
            ['name' => 'available_countries', 'label_ja' => '対応国',   'required' => false, 'type' => 'json'],
            ['name' => 'sort_order',          'label_ja' => '表示順',   'required' => false, 'type' => 'integer'],
            ['name' => 'is_active',           'label_ja' => '有効フラグ','required' => false, 'type' => 'boolean'],
        ],

        'mst_car_series' => [
            ['name' => 'series_id',       'label_ja' => 'シリーズID', 'required' => true, 'type' => 'integer'],
            ['name' => 'series_name',     'label_ja' => 'シリーズ名', 'required' => true, 'type' => 'string'],
            ['name' => 'manufacturer_id', 'label_ja' => 'メーカーID', 'required' => true, 'type' => 'integer'],
        ],

        'mst_car_series_body_types' => [
            ['name' => 'id',                'label_ja' => 'ID',                 'required' => true,  'type' => 'integer'],
            ['name' => 'series_id',         'label_ja' => 'シリーズ名',         'required' => true,  'type' => 'string'],
            ['name' => 'manufacturer_name', 'label_ja' => 'メーカー名',         'required' => true,  'type' => 'string'],
            ['name' => 'body_type_id',      'label_ja' => 'ボディタイプ名',     'required' => true,  'type' => 'string'],
            ['name' => 'is_primary',        'label_ja' => '代表フラグ',         'required' => false, 'type' => 'boolean'],
            ['name' => 'sort_order',        'label_ja' => '表示順',             'required' => false, 'type' => 'integer'],
        ],

        'mst_car_type_options' => [
            ['name' => 'id',         'label_ja' => 'ID',        'required' => true,  'type' => 'integer'],
            ['name' => 'value',      'label_ja' => '値',        'required' => true,  'type' => 'string'],
            ['name' => 'label',      'label_ja' => '表示名',    'required' => true,  'type' => 'string'],
            ['name' => 'sort_order', 'label_ja' => '表示順',    'required' => false, 'type' => 'integer'],
            ['name' => 'is_active',  'label_ja' => '有効フラグ','required' => false, 'type' => 'boolean'],
        ],

        'mst_color_options' => [
            ['name' => 'id',         'label_ja' => 'ID',          'required' => true,  'type' => 'integer'],
            ['name' => 'value',      'label_ja' => '値',          'required' => true,  'type' => 'string'],
            ['name' => 'label',      'label_ja' => '表示名',      'required' => true,  'type' => 'string'],
            ['name' => 'hex_code',   'label_ja' => 'カラーコード','required' => false, 'type' => 'string'],
            ['name' => 'group',      'label_ja' => '色系統',      'required' => false, 'type' => 'string'],
            ['name' => 'sort_order', 'label_ja' => '表示順',      'required' => false, 'type' => 'integer'],
            ['name' => 'is_active',  'label_ja' => '有効フラグ',  'required' => false, 'type' => 'boolean'],
        ],

        'mst_detail_options' => [
            ['name' => 'id',         'label_ja' => 'ID',        'required' => true,  'type' => 'integer'],
            ['name' => 'value',      'label_ja' => '値',        'required' => true,  'type' => 'string'],
            ['name' => 'label',      'label_ja' => '表示名',    'required' => true,  'type' => 'string'],
            ['name' => 'sort_order', 'label_ja' => '表示順',    'required' => false, 'type' => 'integer'],
            ['name' => 'is_active',  'label_ja' => '有効フラグ','required' => false, 'type' => 'boolean'],
        ],

        'mst_displacement_lists' => [
            ['name' => 'id',           'label_ja' => 'ID',         'required' => true,  'type' => 'integer'],
            ['name' => 'name',         'label_ja' => '名称',       'required' => true,  'type' => 'string'],
            ['name' => 'min_amount',   'label_ja' => '最小排気量', 'required' => false, 'type' => 'decimal'],
            ['name' => 'max_amount',   'label_ja' => '最大排気量', 'required' => false, 'type' => 'decimal'],
            ['name' => 'is_unlimited', 'label_ja' => '上限なし',   'required' => false, 'type' => 'boolean'],
        ],

        'mst_equipment_basic' => [
            ['name' => 'id',         'label_ja' => 'ID',        'required' => true,  'type' => 'integer'],
            ['name' => 'value',      'label_ja' => '値',        'required' => true,  'type' => 'string'],
            ['name' => 'label',      'label_ja' => '表示名',    'required' => true,  'type' => 'string'],
            ['name' => 'sort_order', 'label_ja' => '表示順',    'required' => false, 'type' => 'integer'],
            ['name' => 'is_active',  'label_ja' => '有効フラグ','required' => false, 'type' => 'boolean'],
        ],

        'mst_equipment_dressup' => [
            ['name' => 'id',         'label_ja' => 'ID',        'required' => true,  'type' => 'integer'],
            ['name' => 'value',      'label_ja' => '値',        'required' => true,  'type' => 'string'],
            ['name' => 'label',      'label_ja' => '表示名',    'required' => true,  'type' => 'string'],
            ['name' => 'sort_order', 'label_ja' => '表示順',    'required' => false, 'type' => 'integer'],
            ['name' => 'is_active',  'label_ja' => '有効フラグ','required' => false, 'type' => 'boolean'],
        ],

        'mst_equipment_env' => [
            ['name' => 'id',         'label_ja' => 'ID',        'required' => true,  'type' => 'integer'],
            ['name' => 'value',      'label_ja' => '値',        'required' => true,  'type' => 'string'],
            ['name' => 'label',      'label_ja' => '表示名',    'required' => true,  'type' => 'string'],
            ['name' => 'sort_order', 'label_ja' => '表示順',    'required' => false, 'type' => 'integer'],
            ['name' => 'is_active',  'label_ja' => '有効フラグ','required' => false, 'type' => 'boolean'],
        ],

        'mst_equipment_safety' => [
            ['name' => 'id',         'label_ja' => 'ID',        'required' => true,  'type' => 'integer'],
            ['name' => 'value',      'label_ja' => '値',        'required' => true,  'type' => 'string'],
            ['name' => 'label',      'label_ja' => '表示名',    'required' => true,  'type' => 'string'],
            ['name' => 'sort_order', 'label_ja' => '表示順',    'required' => false, 'type' => 'integer'],
            ['name' => 'is_active',  'label_ja' => '有効フラグ','required' => false, 'type' => 'boolean'],
        ],

        'mst_featured_body_types' => [
            ['name' => 'id',             'label_ja' => 'ID',                 'required' => true,  'type' => 'integer'],
            ['name' => 'body_type_code', 'label_ja' => 'ボディタイプコード', 'required' => true,  'type' => 'string'],
            ['name' => 'position',       'label_ja' => '表示位置',           'required' => true,  'type' => 'string'],
            ['name' => 'sort_order',     'label_ja' => '表示順',             'required' => false, 'type' => 'integer'],
            ['name' => 'is_active',      'label_ja' => '有効フラグ',         'required' => false, 'type' => 'boolean'],
        ],

        'mst_featured_brands' => [
            ['name' => 'id',                 'label_ja' => 'ID',             'required' => true,  'type' => 'integer'],
            ['name' => 'manufacturer_code',  'label_ja' => 'メーカーコード', 'required' => true,  'type' => 'string'],
            ['name' => 'position',           'label_ja' => '表示位置',       'required' => true,  'type' => 'string'],
            ['name' => 'sort_order',         'label_ja' => '表示順',         'required' => false, 'type' => 'integer'],
            ['name' => 'is_active',          'label_ja' => '有効フラグ',     'required' => false, 'type' => 'boolean'],
        ],

        'mst_liability_insurances' => [
            ['name' => 'id',           'label_ja' => 'ID',           'required' => true, 'type' => 'integer'],
            ['name' => 'vehicle_type', 'label_ja' => '車種区分',     'required' => true, 'type' => 'enum', 'values' => ['light', 'standard']],
            ['name' => 'months',       'label_ja' => '保険期間(月)', 'required' => true, 'type' => 'integer'],
            ['name' => 'amount',       'label_ja' => '保険料(円)',   'required' => true, 'type' => 'decimal'],
        ],

        'mst_loan_plans' => [
            ['name' => 'id',             'label_ja' => 'ID',           'required' => true,  'type' => 'integer'],
            ['name' => 'name',           'label_ja' => 'プラン名',     'required' => true,  'type' => 'string'],
            ['name' => 'interest_rate',  'label_ja' => '金利(%)',      'required' => true,  'type' => 'decimal'],
            ['name' => 'months_options', 'label_ja' => '回数選択肢',   'required' => true,  'type' => 'json'],
            ['name' => 'min_months',     'label_ja' => '最小回数',     'required' => true,  'type' => 'integer'],
            ['name' => 'max_months',     'label_ja' => '最大回数',     'required' => true,  'type' => 'integer'],
            ['name' => 'is_default',     'label_ja' => 'デフォルト',   'required' => false, 'type' => 'boolean'],
            ['name' => 'is_active',      'label_ja' => '有効フラグ',   'required' => false, 'type' => 'boolean'],
        ],

        'mst_manufacturer_images' => [
            ['name' => 'id',              'label_ja' => 'ID',           'required' => true,  'type' => 'integer'],
            ['name' => 'manufacturer_id', 'label_ja' => 'メーカーID',   'required' => true,  'type' => 'integer'],
            ['name' => 'image_type',      'label_ja' => '画像種別',     'required' => false, 'type' => 'string'],
            ['name' => 'file_path',       'label_ja' => 'ファイルパス', 'required' => true,  'type' => 'string', 'is_image' => true],
            ['name' => 'alt_text',        'label_ja' => 'ALTテキスト',  'required' => false, 'type' => 'string'],
            ['name' => 'sort_order',      'label_ja' => '表示順',       'required' => false, 'type' => 'integer'],
            ['name' => 'is_main',         'label_ja' => 'メイン画像',   'required' => false, 'type' => 'boolean'],
            ['name' => 'is_active',       'label_ja' => '有効フラグ',   'required' => false, 'type' => 'boolean'],
        ],

        'mst_manufacturers' => [
            ['name' => 'id',           'label_ja' => 'ID',       'required' => true,  'type' => 'integer'],
            ['name' => 'name',         'label_ja' => '名称',     'required' => true,  'type' => 'string'],
            ['name' => 'name_kana',    'label_ja' => '名称カナ', 'required' => false, 'type' => 'string'],
            ['name' => 'display_name', 'label_ja' => '表示名',   'required' => false, 'type' => 'string'],
            ['name' => 'code',         'label_ja' => 'コード',   'required' => true,  'type' => 'string'],
            ['name' => 'url',          'label_ja' => 'URL',      'required' => false, 'type' => 'string'],
            ['name' => 'description',  'label_ja' => '説明',     'required' => false, 'type' => 'string'],
            ['name' => 'country_code', 'label_ja' => '国コード', 'required' => false, 'type' => 'string'],
            ['name' => 'sort_order',   'label_ja' => '表示順',   'required' => false, 'type' => 'integer'],
            ['name' => 'is_active',    'label_ja' => '有効フラグ','required' => false, 'type' => 'boolean'],
        ],

        'mst_mileage_lists' => [
            ['name' => 'id',           'label_ja' => 'ID',       'required' => true,  'type' => 'integer'],
            ['name' => 'name',         'label_ja' => '名称',     'required' => true,  'type' => 'string'],
            ['name' => 'min_amount',   'label_ja' => '最小距離', 'required' => false, 'type' => 'decimal'],
            ['name' => 'max_amount',   'label_ja' => '最大距離', 'required' => false, 'type' => 'decimal'],
            ['name' => 'is_unlimited', 'label_ja' => '上限なし', 'required' => false, 'type' => 'boolean'],
        ],

        'mst_price_lists' => [
            ['name' => 'id',           'label_ja' => 'ID',       'required' => true,  'type' => 'integer'],
            ['name' => 'name',         'label_ja' => '名称',     'required' => true,  'type' => 'string'],
            ['name' => 'max_amount',   'label_ja' => '最大金額', 'required' => false, 'type' => 'decimal'],
            ['name' => 'is_unlimited', 'label_ja' => '上限なし', 'required' => false, 'type' => 'boolean'],
        ],

        'mst_regions' => [
            ['name' => 'id',          'label_ja' => 'ID',               'required' => true,  'type' => 'integer'],
            ['name' => 'area_code',   'label_ja' => 'エリアコード',     'required' => false, 'type' => 'integer'],
            ['name' => 'name',        'label_ja' => '名称',             'required' => true,  'type' => 'string'],
            ['name' => 'url',         'label_ja' => 'URL',              'required' => false, 'type' => 'string'],
            ['name' => 'query_param', 'label_ja' => 'クエリパラメータ', 'required' => false, 'type' => 'string'],
            ['name' => 'sort_order',  'label_ja' => '表示順',           'required' => false, 'type' => 'integer'],
        ],

        'mst_riding_capacity_lists' => [
            ['name' => 'id',           'label_ja' => 'ID',       'required' => true,  'type' => 'integer'],
            ['name' => 'name',         'label_ja' => '名称',     'required' => true,  'type' => 'string'],
            ['name' => 'max_amount',   'label_ja' => '最大人数', 'required' => true,  'type' => 'decimal'],
            ['name' => 'is_unlimited', 'label_ja' => '上限なし', 'required' => false, 'type' => 'boolean'],
        ],

        'mst_seat_options' => [
            ['name' => 'id',         'label_ja' => 'ID',        'required' => true,  'type' => 'integer'],
            ['name' => 'value',      'label_ja' => '値',        'required' => true,  'type' => 'string'],
            ['name' => 'label',      'label_ja' => '表示名',    'required' => true,  'type' => 'string'],
            ['name' => 'sort_order', 'label_ja' => '表示順',    'required' => false, 'type' => 'integer'],
            ['name' => 'is_active',  'label_ja' => '有効フラグ','required' => false, 'type' => 'boolean'],
        ],

        'mst_vehicle_weight_taxes' => [
            ['name' => 'id',          'label_ja' => 'ID',            'required' => true,  'type' => 'integer'],
            ['name' => 'weight_from', 'label_ja' => '重量下限(kg)',  'required' => true,  'type' => 'integer'],
            ['name' => 'weight_to',   'label_ja' => '重量上限(kg)',  'required' => true,  'type' => 'integer'],
            ['name' => 'is_light',    'label_ja' => '軽自動車フラグ','required' => false, 'type' => 'boolean'],
            ['name' => 'amount',      'label_ja' => '重量税(円)',    'required' => true,  'type' => 'decimal'],
        ],

        'mst_vehicle_year_versions' => [
            ['name' => 'vehicle_id',          'label_ja' => '車両ID',           'required' => true,  'type' => 'string'],
            ['name' => 'year_from',           'label_ja' => '年式開始',         'required' => true,  'type' => 'integer'],
            ['name' => 'year_to',             'label_ja' => '年式終了',         'required' => false, 'type' => 'integer'],
            ['name' => 'displacement_cc',     'label_ja' => '排気量(cc)',       'required' => false, 'type' => 'integer'],
            ['name' => 'drive_type',          'label_ja' => '駆動方式',         'required' => false, 'type' => 'enum', 'values' => ['2WD', '4WD', 'FR', 'FF']],
            ['name' => 'fuel_efficiency_from','label_ja' => '燃費下限',         'required' => false, 'type' => 'decimal'],
            ['name' => 'fuel_efficiency_to',  'label_ja' => '燃費上限',         'required' => false, 'type' => 'decimal'],
            ['name' => 'max_power_kw',        'label_ja' => '最大出力(kw)',     'required' => false, 'type' => 'integer'],
            ['name' => 'transmission_type',   'label_ja' => 'トランスミッション','required' => false, 'type' => 'enum', 'values' => ['AT', 'MT', 'CVT']],
            ['name' => 'weight_kg',           'label_ja' => '車重(kg)',         'required' => false, 'type' => 'integer'],
            ['name' => 'price_range_from',    'label_ja' => '価格下限',         'required' => false, 'type' => 'decimal'],
            ['name' => 'price_range_to',      'label_ja' => '価格上限',         'required' => false, 'type' => 'decimal'],
            ['name' => 'is_latest',           'label_ja' => '最新フラグ',       'required' => false, 'type' => 'boolean'],
        ],

        'mst_vehicles' => [
            ['name' => 'id',               'label_ja' => 'ID',             'required' => true,  'type' => 'integer'],
            ['name' => 'series_id',        'label_ja' => 'シリーズ名',     'required' => true,  'type' => 'string'],
            ['name' => 'manufacturer_id',  'label_ja' => 'メーカー名',     'required' => true,  'type' => 'string'],
            ['name' => 'name',             'label_ja' => '名称',           'required' => true,  'type' => 'string'],
            ['name' => 'model_code',       'label_ja' => 'モデルコード',   'required' => false, 'type' => 'string'],
            ['name' => 'body_type',        'label_ja' => 'ボディタイプ名', 'required' => false, 'type' => 'string'],
            ['name' => 'country_code',     'label_ja' => '国コード',       'required' => false, 'type' => 'string'],
            ['name' => 'status',           'label_ja' => 'ステータス',     'required' => false, 'type' => 'enum', 'values' => ['active', 'discontinued', 'concept']],
        ],

        'mst_car_series_images' => [
            ['name' => 'id',                'label_ja' => 'ID',            'required' => true,  'type' => 'integer'],
            ['name' => 'series_id',         'label_ja' => 'シリーズ名',     'required' => true,  'type' => 'string'],
            ['name' => 'file_path',         'label_ja' => 'ファイルパス',   'required' => false,  'type' => 'string', 'is_image' => true],
            ['name' => 'alt_text',          'label_ja' => 'ALTテキスト',    'required' => false, 'type' => 'string'],
            ['name' => 'sort_order',        'label_ja' => '表示順',         'required' => false, 'type' => 'integer'],
            ['name' => 'is_main',           'label_ja' => 'メイン画像',     'required' => false, 'type' => 'boolean'],
            ['name' => 'is_active',         'label_ja' => '有効フラグ',     'required' => false, 'type' => 'boolean'],
        ],
    ];

    public static function getColumns(string $tableName): array
    {
        return self::COLUMNS[$tableName] ?? [];
    }

    public static function getImageColumns(string $tableName): array
    {
        return array_filter(
            self::getColumns($tableName),
            fn ($col) => $col['is_image'] ?? false
        );
    }
}