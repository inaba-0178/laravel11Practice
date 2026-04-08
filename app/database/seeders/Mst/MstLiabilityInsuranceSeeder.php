<?php

namespace Database\Seeders\Mst;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MstLiabilityInsuranceSeeder extends Seeder
{
    public function run(): void
    {
        \Log::info('MstVehicleWeightTaxSeeder start');
        DB::connection('mst')->table('mst_liability_insurances')->insert([
            // 軽自動車
            ['vehicle_type' => 'light', 'months' => 12, 'amount' => 7410,  'created_at' => now(), 'updated_at' => now()],
            ['vehicle_type' => 'light', 'months' => 13, 'amount' => 7550,  'created_at' => now(), 'updated_at' => now()],
            ['vehicle_type' => 'light', 'months' => 24, 'amount' => 9950,  'created_at' => now(), 'updated_at' => now()],
            ['vehicle_type' => 'light', 'months' => 25, 'amount' => 10090, 'created_at' => now(), 'updated_at' => now()],
            ['vehicle_type' => 'light', 'months' => 36, 'amount' => 12340, 'created_at' => now(), 'updated_at' => now()],
            ['vehicle_type' => 'light', 'months' => 37, 'amount' => 12480, 'created_at' => now(), 'updated_at' => now()],
            // 普通車
            ['vehicle_type' => 'standard', 'months' => 12, 'amount' => 11780, 'created_at' => now(), 'updated_at' => now()],
            ['vehicle_type' => 'standard', 'months' => 13, 'amount' => 12010, 'created_at' => now(), 'updated_at' => now()],
            ['vehicle_type' => 'standard', 'months' => 24, 'amount' => 17650, 'created_at' => now(), 'updated_at' => now()],
            ['vehicle_type' => 'standard', 'months' => 25, 'amount' => 17880, 'created_at' => now(), 'updated_at' => now()],
            ['vehicle_type' => 'standard', 'months' => 36, 'amount' => 23440, 'created_at' => now(), 'updated_at' => now()],
            ['vehicle_type' => 'standard', 'months' => 37, 'amount' => 23670, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}