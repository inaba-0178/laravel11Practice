<?php

namespace Database\Seeders\Mst;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MstCarTypeOptionSeeder extends Seeder
{
    public function run(): void
    {
        DB::connection('mst')->table('mst_car_type_options')->truncate();

        $records = [
            ['value' => 'domestic',    'label' => '国産車',           'sort_order' => 1],
            ['value' => 'import',      'label' => '輸入車',           'sort_order' => 2],
            ['value' => 'reimport',    'label' => '逆輸入車',         'sort_order' => 3],
            ['value' => 'welfare',     'label' => '福祉車両',         'sort_order' => 4],
            ['value' => 'cold_region', 'label' => '寒冷地仕様車',     'sort_order' => 5],
            ['value' => 'camping',     'label' => 'キャンピングカー', 'sort_order' => 6],
            ['value' => 'commercial',  'label' => '商用車・バン',     'sort_order' => 7],
        ];

        foreach ($records as &$record) {
            $record['is_active']  = true;
            $record['created_at'] = now();
            $record['updated_at'] = now();
        }

        DB::connection('mst')->table('mst_car_type_options')->insert($records);
    }
}