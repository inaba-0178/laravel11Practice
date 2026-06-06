<?php

namespace Database\Seeders\Mst;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MstVehicleTaxesSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        // マイグレーションのup()末尾に追加
        DB::connection('mst')->table('mst_vehicle_taxes')->insert([
            // 通常車両
            ['displacement_list_id' => 1, 'is_light' => false, 'amount' => 25000, 'version_id' => 5, 'created_at' => now(), 'updated_at' => now()], // 800cc以下
            ['displacement_list_id' => 2, 'is_light' => false, 'amount' => 30500, 'version_id' => 5, 'created_at' => now(), 'updated_at' => now()], // 1,000〜1,400cc
            ['displacement_list_id' => 3, 'is_light' => false, 'amount' => 36000, 'version_id' => 5, 'created_at' => now(), 'updated_at' => now()], // 1,500〜1,900cc
            ['displacement_list_id' => 4, 'is_light' => false, 'amount' => 43500, 'version_id' => 5, 'created_at' => now(), 'updated_at' => now()], // 2,000〜2,400cc
            ['displacement_list_id' => 5, 'is_light' => false, 'amount' => 50000, 'version_id' => 5, 'created_at' => now(), 'updated_at' => now()], // 2,500〜2,900cc
            ['displacement_list_id' => 6, 'is_light' => false, 'amount' => 57000, 'version_id' => 5, 'created_at' => now(), 'updated_at' => now()], // 3,000〜4,000cc
            ['displacement_list_id' => 7, 'is_light' => false, 'amount' => 87000, 'version_id' => 5, 'created_at' => now(), 'updated_at' => now()], // 4,000cc以上

            // 軽自動車
            ['displacement_list_id' => 1, 'is_light' => true,  'amount' => 10800, 'version_id' => 5, 'created_at' => now(), 'updated_at' => now()], // 800cc以下（軽）
        ]);
    }
}