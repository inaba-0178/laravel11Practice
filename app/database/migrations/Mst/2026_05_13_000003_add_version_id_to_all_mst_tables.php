<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $connection = 'mst';

    private array $tables = [
        'mst_areas',
        'mst_basic_options',
        'mst_body_type_images',
        'mst_body_types',
        'mst_car_series',
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
        foreach ($this->tables as $table) {
            Schema::connection('mst')->table($table, function (Blueprint $t) use ($table) {
                // mst_car_seriesだけafterの位置が違う
                $afterColumn = $table === 'mst_car_series' ? 'series_id' : 'id';
                
                $t->unsignedBigInteger('version_id')
                ->nullable()
                ->comment('バージョンID')
                ->after($afterColumn);
            });
        }
    }

    public function down(): void
    {
        foreach ($this->tables as $table) {
            Schema::connection('mst')->table($table, function (Blueprint $t) {
                $t->dropColumn('version_id');
            });
        }
    }
};