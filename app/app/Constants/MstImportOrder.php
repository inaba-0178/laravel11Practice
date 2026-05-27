<?php

declare(strict_types=1);

namespace App\Constants;

class MstImportOrder
{
    /**
     * マスタテーブルの投入順序
     *
     * 外部キー制約の依存関係を考慮した順序で定義。
     * 新規テーブル追加時は依存関係に注意して追加すること。
     *
     * 依存関係：
     *   mst_car_series → mst_vehicles → mst_vehicle_year_versions
     *   mst_manufacturers → mst_manufacturer_images
     *   mst_body_types → mst_body_type_images
     */
    public const ORDER = [
        'mst_areas',
        'mst_regions',
        'mst_manufacturers',
        'mst_manufacturer_images',
        'mst_body_types',
        'mst_body_type_images',
        'mst_featured_body_types',
        'mst_featured_brands',
        'mst_car_series',
        'mst_car_series_images',
        'mst_car_series_body_types',
        'mst_vehicles',
        'mst_vehicle_year_versions',
        'mst_basic_options',
        'mst_car_type_options',
        'mst_color_options',
        'mst_detail_options',
        'mst_seat_options',
        'mst_equipment_basic',
        'mst_equipment_dressup',
        'mst_equipment_env',
        'mst_equipment_safety',
        'mst_displacement_lists',
        'mst_mileage_lists',
        'mst_price_lists',
        'mst_riding_capacity_lists',
        'mst_liability_insurances',
        'mst_loan_plans',
        'mst_vehicle_weight_taxes',
    ];
}