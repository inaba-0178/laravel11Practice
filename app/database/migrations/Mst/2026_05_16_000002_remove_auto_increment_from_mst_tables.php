<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    protected $connection = 'mst';

    private const TABLES = [
        'mst_areas',
        'mst_basic_options',
        'mst_body_type_images',
        'mst_body_types',
        'mst_car_series_body_types',
        'mst_car_type_options',
        'mst_color_options',
        'mst_detail_options',
        'mst_displacement_lists',
        'mst_equipment_basic',
        'mst_equipment_dressup',
        'mst_equipment_env',
        'mst_equipment_safety',
        'mst_featured_body_types',
        'mst_featured_brands',
        'mst_liability_insurances',
        'mst_loan_plans',
        'mst_manufacturer_images',
        'mst_manufacturers',
        'mst_mileage_lists',
        'mst_price_lists',
        'mst_regions',
        'mst_riding_capacity_lists',
        'mst_seat_options',
        'mst_vehicle_weight_taxes',
        'mst_vehicle_year_versions',
        'mst_vehicles',
    ];

    public function up(): void
    {
        foreach (self::TABLES as $table) {
            // AUTO_INCREMENTを削除（PRIMARY KEYは残す）
            DB::connection('mst')->statement(
                "ALTER TABLE `{$table}` MODIFY `id` BIGINT(20) UNSIGNED NOT NULL"
            );
        }
    }

    public function down(): void
    {
        foreach (self::TABLES as $table) {
            DB::connection('mst')->statement(
                "ALTER TABLE `{$table}` MODIFY `id` BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT"
            );
        }
    }
};