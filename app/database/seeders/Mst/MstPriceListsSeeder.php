<?php

namespace Database\Seeders\Mst;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MstPriceListsSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        DB::connection('mst')->table('mst_price_lists')->insert([
            ['name' => '〜30万円',   'max_amount' => 300000,  'is_unlimited' => 0, 'created_at' => $now, 'updated_at' => $now],
            ['name' => '〜50万円',   'max_amount' => 500000,  'is_unlimited' => 0, 'created_at' => $now, 'updated_at' => $now],
            ['name' => '〜100万円',  'max_amount' => 1000000, 'is_unlimited' => 0, 'created_at' => $now, 'updated_at' => $now],
            ['name' => '〜200万円',  'max_amount' => 2000000, 'is_unlimited' => 0, 'created_at' => $now, 'updated_at' => $now],
            ['name' => '〜300万円',  'max_amount' => 3000000, 'is_unlimited' => 0, 'created_at' => $now, 'updated_at' => $now],
            ['name' => '500万円以上','max_amount' => 5000000, 'is_unlimited' => 1, 'created_at' => $now, 'updated_at' => $now],
        ]);
    }
}