<?php

namespace Database\Seeders\Mst;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MstFeaturedBodyTypesSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        DB::connection('mst')->table('mst_featured_body_types')->insert([
            // 上段
            ['body_type_code' => 'KeiCars',                   'position' => 'top-hi',  'sort_order' => 100, 'is_active' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['body_type_code' => 'CompactCars',                'position' => 'top-hi',  'sort_order' => 110, 'is_active' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['body_type_code' => 'Minivans',                   'position' => 'top-hi',  'sort_order' => 120, 'is_active' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['body_type_code' => 'StationWagons',              'position' => 'top-hi',  'sort_order' => 130, 'is_active' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['body_type_code' => 'SUVs&CrossCountryVehicles',  'position' => 'top-hi',  'sort_order' => 140, 'is_active' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['body_type_code' => 'Sedans',                     'position' => 'top-hi',  'sort_order' => 150, 'is_active' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['body_type_code' => 'Campers',                    'position' => 'top-hi',  'sort_order' => 160, 'is_active' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['body_type_code' => 'Coupes',                     'position' => 'top-hi',  'sort_order' => 170, 'is_active' => 1, 'created_at' => $now, 'updated_at' => $now],
            // 下段
            ['body_type_code' => 'Hybrid',                     'position' => 'top-row', 'sort_order' => 200, 'is_active' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['body_type_code' => 'Hatchback',                  'position' => 'top-row', 'sort_order' => 210, 'is_active' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['body_type_code' => 'Convertibles',               'position' => 'top-row', 'sort_order' => 220, 'is_active' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['body_type_code' => 'PickupTrucks',               'position' => 'top-row', 'sort_order' => 230, 'is_active' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['body_type_code' => 'WelfareVehicles',            'position' => 'top-row', 'sort_order' => 240, 'is_active' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['body_type_code' => 'CommercialVehicles&Vans',    'position' => 'top-row', 'sort_order' => 250, 'is_active' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['body_type_code' => 'Trucks',                     'position' => 'top-row', 'sort_order' => 260, 'is_active' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['body_type_code' => 'Other',                      'position' => 'top-row', 'sort_order' => 999, 'is_active' => 1, 'created_at' => $now, 'updated_at' => $now],
        ]);
    }
}