<?php

namespace Database\Seeders\Mst;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MstDisplacementListsSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        DB::connection('mst')->table('mst_displacement_lists')->insert([
            ['name' => '800cc以下',        'min_amount' => 0,    'max_amount' => 800,  'is_unlimited' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['name' => '1,000〜1,400cc以下','min_amount' => 1000, 'max_amount' => 1400, 'is_unlimited' => 0, 'created_at' => $now, 'updated_at' => $now],
            ['name' => '1,500〜1,900cc以下','min_amount' => 1500, 'max_amount' => 1900, 'is_unlimited' => 0, 'created_at' => $now, 'updated_at' => $now],
            ['name' => '2,000〜2,400cc以下','min_amount' => 2000, 'max_amount' => 2400, 'is_unlimited' => 0, 'created_at' => $now, 'updated_at' => $now],
            ['name' => '2,500〜2,900cc以下','min_amount' => 2500, 'max_amount' => 2900, 'is_unlimited' => 0, 'created_at' => $now, 'updated_at' => $now],
            ['name' => '3,000〜4,000cc以下','min_amount' => 3000, 'max_amount' => 4000, 'is_unlimited' => 0, 'created_at' => $now, 'updated_at' => $now],
            ['name' => '4,000cc以上',      'min_amount' => 4000, 'max_amount' => null, 'is_unlimited' => 1, 'created_at' => $now, 'updated_at' => $now],
        ]);
    }
}