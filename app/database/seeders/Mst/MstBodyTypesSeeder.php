<?php

namespace Database\Seeders\Mst;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MstBodyTypesSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        DB::connection('mst')->table('mst_body_types')->insert([
            ['name' => '軽自動車',         'name_kana' => '軽自動車',         'code' => 'KeiCars',                    'description' => null, 'available_countries' => '["JP"]', 'sort_order' => 100,  'is_active' => 1, 'created_at' => $now, 'updated_at' => $now, 'deleted_at' => null],
            ['name' => 'コンパクトカー',   'name_kana' => 'コンパクトカー',   'code' => 'CompactCars',                'description' => null, 'available_countries' => '["JP"]', 'sort_order' => 110,  'is_active' => 1, 'created_at' => $now, 'updated_at' => $now, 'deleted_at' => null],
            ['name' => 'ミニバン',         'name_kana' => 'ミニバン',         'code' => 'Minivans',                   'description' => null, 'available_countries' => '["JP"]', 'sort_order' => 120,  'is_active' => 1, 'created_at' => $now, 'updated_at' => $now, 'deleted_at' => null],
            ['name' => 'ステーションワゴン','name_kana' => 'ステーションワゴン','code' => 'StationWagons',             'description' => null, 'available_countries' => '["JP"]', 'sort_order' => 130,  'is_active' => 1, 'created_at' => $now, 'updated_at' => $now, 'deleted_at' => null],
            ['name' => 'SUV・クロカン',    'name_kana' => 'SUV・クロカン',    'code' => 'SUVs&CrossCountryVehicles',  'description' => null, 'available_countries' => '["JP"]', 'sort_order' => 140,  'is_active' => 1, 'created_at' => $now, 'updated_at' => $now, 'deleted_at' => null],
            ['name' => 'セダン',           'name_kana' => 'セダン',           'code' => 'Sedans',                     'description' => null, 'available_countries' => '["JP"]', 'sort_order' => 150,  'is_active' => 1, 'created_at' => $now, 'updated_at' => $now, 'deleted_at' => null],
            ['name' => 'キャンピングカー', 'name_kana' => 'キャンピングカー', 'code' => 'Campers',                    'description' => null, 'available_countries' => '["JP"]', 'sort_order' => 160,  'is_active' => 1, 'created_at' => $now, 'updated_at' => $now, 'deleted_at' => null],
            ['name' => 'クーペ',           'name_kana' => 'クーペ',           'code' => 'Coupes',                     'description' => null, 'available_countries' => '["JP"]', 'sort_order' => 170,  'is_active' => 1, 'created_at' => $now, 'updated_at' => $now, 'deleted_at' => null],
            ['name' => 'ハイブリッド',     'name_kana' => 'ハイブリッド',     'code' => 'Hybrid',                     'description' => null, 'available_countries' => '["JP"]', 'sort_order' => 200,  'is_active' => 1, 'created_at' => $now, 'updated_at' => $now, 'deleted_at' => null],
            ['name' => 'ハッチバッグ',     'name_kana' => 'ハッチバッグ',     'code' => 'Hatchback',                  'description' => null, 'available_countries' => '["JP"]', 'sort_order' => 210,  'is_active' => 1, 'created_at' => $now, 'updated_at' => $now, 'deleted_at' => null],
            ['name' => 'オープンカー',     'name_kana' => 'オープンカー',     'code' => 'Convertibles',               'description' => null, 'available_countries' => '["JP"]', 'sort_order' => 220,  'is_active' => 1, 'created_at' => $now, 'updated_at' => $now, 'deleted_at' => null],
            ['name' => 'ピックアップトラック','name_kana' => 'ピックアップトラック','code' => 'PickupTrucks',          'description' => null, 'available_countries' => '["JP"]', 'sort_order' => 230,  'is_active' => 1, 'created_at' => $now, 'updated_at' => $now, 'deleted_at' => null],
            ['name' => '福祉車両',         'name_kana' => '福祉車両',         'code' => 'WelfareVehicles',             'description' => null, 'available_countries' => '["JP"]', 'sort_order' => 240,  'is_active' => 1, 'created_at' => $now, 'updated_at' => $now, 'deleted_at' => null],
            ['name' => '商用車・バン',     'name_kana' => '商用車・バン',     'code' => 'CommercialVehicles&Vans',     'description' => null, 'available_countries' => '["JP"]', 'sort_order' => 250,  'is_active' => 1, 'created_at' => $now, 'updated_at' => $now, 'deleted_at' => null],
            ['name' => 'トラック',         'name_kana' => 'トラック',         'code' => 'Trucks',                     'description' => null, 'available_countries' => '["JP"]', 'sort_order' => 260,  'is_active' => 1, 'created_at' => $now, 'updated_at' => $now, 'deleted_at' => null],
            ['name' => 'その他',           'name_kana' => 'その他',           'code' => 'Other',                      'description' => null, 'available_countries' => '["JP"]', 'sort_order' => 999,  'is_active' => 1, 'created_at' => $now, 'updated_at' => $now, 'deleted_at' => null],
        ]);
    }
}