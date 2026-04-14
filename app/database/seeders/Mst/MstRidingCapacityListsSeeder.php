<?php

namespace Database\Seeders\Mst;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MstRidingCapacityListsSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        DB::connection('mst')->table('mst_riding_capacity_lists')->insert([
            ['name' => '2人乗り',  'max_amount' => 2,  'is_unlimited' => 0, 'created_at' => $now, 'updated_at' => $now],
            ['name' => '4人乗り',  'max_amount' => 4,  'is_unlimited' => 0, 'created_at' => $now, 'updated_at' => $now],
            ['name' => '5人乗り',  'max_amount' => 5,  'is_unlimited' => 0, 'created_at' => $now, 'updated_at' => $now],
            ['name' => '6人乗り',  'max_amount' => 6,  'is_unlimited' => 0, 'created_at' => $now, 'updated_at' => $now],
            ['name' => '7人乗り',  'max_amount' => 7,  'is_unlimited' => 0, 'created_at' => $now, 'updated_at' => $now],
            ['name' => '8人乗り',  'max_amount' => 8,  'is_unlimited' => 0, 'created_at' => $now, 'updated_at' => $now],
            ['name' => '10人乗り', 'max_amount' => 10, 'is_unlimited' => 0, 'created_at' => $now, 'updated_at' => $now],
        ]);
    }
}