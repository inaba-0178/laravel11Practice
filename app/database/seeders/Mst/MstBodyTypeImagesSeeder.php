<?php

namespace Database\Seeders\Mst;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MstBodyTypeImagesSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        DB::connection('mst')->table('mst_body_type_images')->insert([
            ['body_type_id' => 1,  'image_type' => 'logo', 'file_path' => '/imgs/cartype/KeiCars.png',                      'alt_text' => '軽自動車',           'sort_order' => 100, 'is_main' => 1, 'is_active' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['body_type_id' => 2,  'image_type' => 'logo', 'file_path' => '/imgs/cartype/CompactCars.png',                  'alt_text' => 'コンパクトカー',     'sort_order' => 110, 'is_main' => 1, 'is_active' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['body_type_id' => 3,  'image_type' => 'logo', 'file_path' => '/imgs/cartype/Minivans.png',                     'alt_text' => 'ミニバン',           'sort_order' => 120, 'is_main' => 1, 'is_active' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['body_type_id' => 4,  'image_type' => 'logo', 'file_path' => '/imgs/cartype/StationWagons.png',                'alt_text' => 'ステーションワゴン', 'sort_order' => 130, 'is_main' => 1, 'is_active' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['body_type_id' => 5,  'image_type' => 'logo', 'file_path' => '/imgs/cartype/SUVs&CrossCountryVehicles.png',    'alt_text' => 'SUV・クロカン',      'sort_order' => 140, 'is_main' => 1, 'is_active' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['body_type_id' => 6,  'image_type' => 'logo', 'file_path' => '/imgs/cartype/Sedans.png',                       'alt_text' => 'セダン',             'sort_order' => 150, 'is_main' => 1, 'is_active' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['body_type_id' => 7,  'image_type' => 'logo', 'file_path' => '/imgs/cartype/Campers.png',                      'alt_text' => 'キャンピングカー',   'sort_order' => 160, 'is_main' => 1, 'is_active' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['body_type_id' => 8,  'image_type' => 'logo', 'file_path' => '/imgs/cartype/Coupes.png',                       'alt_text' => 'クーペ',             'sort_order' => 170, 'is_main' => 1, 'is_active' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['body_type_id' => 9,  'image_type' => 'logo', 'file_path' => '/imgs/cartype/Hybrid.png',                       'alt_text' => 'ハイブリッド',       'sort_order' => 200, 'is_main' => 1, 'is_active' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['body_type_id' => 10, 'image_type' => 'logo', 'file_path' => '/imgs/cartype/Hatchback.png',                    'alt_text' => 'ハッチバッグ',       'sort_order' => 210, 'is_main' => 1, 'is_active' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['body_type_id' => 11, 'image_type' => 'logo', 'file_path' => '/imgs/cartype/Convertibles.png',                 'alt_text' => 'オープンカー',       'sort_order' => 220, 'is_main' => 1, 'is_active' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['body_type_id' => 12, 'image_type' => 'logo', 'file_path' => '/imgs/cartype/PickupTrucks.png',                 'alt_text' => 'ピックアップトラック','sort_order' => 230, 'is_main' => 1, 'is_active' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['body_type_id' => 13, 'image_type' => 'logo', 'file_path' => '/imgs/cartype/WelfareVehicles.png',              'alt_text' => '福祉車両',           'sort_order' => 240, 'is_main' => 1, 'is_active' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['body_type_id' => 14, 'image_type' => 'logo', 'file_path' => '/imgs/cartype/CommercialVehicles&Vans.png',      'alt_text' => '商用車・バン',       'sort_order' => 250, 'is_main' => 1, 'is_active' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['body_type_id' => 15, 'image_type' => 'logo', 'file_path' => '/imgs/cartype/Trucks.png',                       'alt_text' => 'トラック',           'sort_order' => 260, 'is_main' => 1, 'is_active' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['body_type_id' => 16, 'image_type' => 'logo', 'file_path' => '/imgs/cartype/Other.png',                        'alt_text' => 'その他',             'sort_order' => 999, 'is_main' => 1, 'is_active' => 1, 'created_at' => $now, 'updated_at' => $now],
        ]);
    }
}