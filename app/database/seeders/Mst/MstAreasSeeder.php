<?php

namespace Database\Seeders\Mst;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MstAreasSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        DB::connection('mst')->table('mst_areas')->insert([
            ['name' => '北海道',     'query_param' => 'hokkaido', 'sort_order' => 1,  'created_at' => $now, 'updated_at' => $now],
            ['name' => '東北',       'query_param' => 'touhoku',  'sort_order' => 2,  'created_at' => $now, 'updated_at' => $now],
            ['name' => '関東',       'query_param' => 'kantou',   'sort_order' => 3,  'created_at' => $now, 'updated_at' => $now],
            ['name' => '関西',       'query_param' => 'kansai',   'sort_order' => 4,  'created_at' => $now, 'updated_at' => $now],
            ['name' => '四国',       'query_param' => 'sikoku',   'sort_order' => 5,  'created_at' => $now, 'updated_at' => $now],
            ['name' => '北陸・甲信越', 'query_param' => 'hokuriku', 'sort_order' => 6,  'created_at' => $now, 'updated_at' => $now],
            ['name' => '東海',       'query_param' => 'toukai',   'sort_order' => 7,  'created_at' => $now, 'updated_at' => $now],
            ['name' => '中国',       'query_param' => 'tyuugoku', 'sort_order' => 8,  'created_at' => $now, 'updated_at' => $now],
            ['name' => '九州',       'query_param' => 'kyuusyu',  'sort_order' => 9,  'created_at' => $now, 'updated_at' => $now],
            ['name' => '沖縄',       'query_param' => 'okinaha',  'sort_order' => 10, 'created_at' => $now, 'updated_at' => $now],
        ]);
    }
}