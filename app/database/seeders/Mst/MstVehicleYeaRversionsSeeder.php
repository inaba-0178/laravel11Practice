<?php

namespace Database\Seeders\Mst;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MstVehicleYearVersionsSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        DB::connection('mst')->table('mst_vehicle_year_versions')->insert([
            ['vehicle_id' => 1, 'year_from' => 2011, 'year_to' => 2014, 'displacement_cc' => 1797, 'drive_type' => 'FF', 'fuel_efficiency_from' => 26.6, 'fuel_efficiency_to' => 30.4, 'max_power_kw' => 100, 'transmission_type' => 'CVT', 'weight_kg' => 1460, 'price_range_from' => 3550000, 'price_range_to' => 4880000, 'is_latest' => 0, 'created_at' => $now, 'updated_at' => $now, 'deleted_at' => null],
            ['vehicle_id' => 2, 'year_from' => 2014, 'year_to' => 2017, 'displacement_cc' => 1797, 'drive_type' => 'FF', 'fuel_efficiency_from' => 26.6, 'fuel_efficiency_to' => 30.4, 'max_power_kw' => 100, 'transmission_type' => 'CVT', 'weight_kg' => 1460, 'price_range_from' => 3550000, 'price_range_to' => 4880000, 'is_latest' => 0, 'created_at' => $now, 'updated_at' => $now, 'deleted_at' => null],
            ['vehicle_id' => 3, 'year_from' => 2011, 'year_to' => 2023, 'displacement_cc' => 1797, 'drive_type' => 'FF', 'fuel_efficiency_from' => 23.9, 'fuel_efficiency_to' => 26.6, 'max_power_kw' => 100, 'transmission_type' => 'CVT', 'weight_kg' => 1440, 'price_range_from' => 3860000, 'price_range_to' => 4880000, 'is_latest' => 0, 'created_at' => $now, 'updated_at' => $now, 'deleted_at' => null],
        ]);
    }
}