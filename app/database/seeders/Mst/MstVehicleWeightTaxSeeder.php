<?php

namespace Database\Seeders\Mst;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MstVehicleWeightTaxSeeder extends Seeder
{
    public function run(): void
    {
        DB::connection('mst')->table('mst_vehicle_weight_taxes')->insert([
            // 軽自動車
            ['weight_from' => 0,    'weight_to' => 9999, 'is_light' => true,  'amount' => 6600,  'created_at' => now(), 'updated_at' => now()],
            // 普通車
            ['weight_from' => 0,    'weight_to' => 500,  'is_light' => false, 'amount' => 5000,  'created_at' => now(), 'updated_at' => now()],
            ['weight_from' => 500,  'weight_to' => 1000, 'is_light' => false, 'amount' => 10000, 'created_at' => now(), 'updated_at' => now()],
            ['weight_from' => 1000, 'weight_to' => 1500, 'is_light' => false, 'amount' => 15000, 'created_at' => now(), 'updated_at' => now()],
            ['weight_from' => 1500, 'weight_to' => 2000, 'is_light' => false, 'amount' => 20000, 'created_at' => now(), 'updated_at' => now()],
            ['weight_from' => 2000, 'weight_to' => 2500, 'is_light' => false, 'amount' => 25000, 'created_at' => now(), 'updated_at' => now()],
            ['weight_from' => 2500, 'weight_to' => 3000, 'is_light' => false, 'amount' => 30000, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}