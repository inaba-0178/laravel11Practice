<?php

namespace Database\Seeders\Mst;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MstMileageListsSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        DB::connection('mst')->table('mst_mileage_lists')->insert([
            ['name' => '1万km以上',   'min_amount' => null,   'max_amount' => 9999,   'is_unlimited' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['name' => '1〜3万km',    'min_amount' => 10000,  'max_amount' => 29999,  'is_unlimited' => 0, 'created_at' => $now, 'updated_at' => $now],
            ['name' => '3〜5万km',    'min_amount' => 30000,  'max_amount' => 49999,  'is_unlimited' => 0, 'created_at' => $now, 'updated_at' => $now],
            ['name' => '5〜10万km',   'min_amount' => 50000,  'max_amount' => 99999,  'is_unlimited' => 0, 'created_at' => $now, 'updated_at' => $now],
            ['name' => '10〜15万km',  'min_amount' => 100000, 'max_amount' => 149999, 'is_unlimited' => 0, 'created_at' => $now, 'updated_at' => $now],
            ['name' => '15万km',      'min_amount' => 150000, 'max_amount' => null,   'is_unlimited' => 1, 'created_at' => $now, 'updated_at' => $now],
        ]);
    }
}