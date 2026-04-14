<?php

namespace Database\Seeders\Mst;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MstManufacturerImagesSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        DB::connection('mst')->table('mst_manufacturer_images')->insert([
            ['manufacturer_id' => 1,  'image_type' => 'logo', 'file_path' => '/imgs/maker/maker_lexus.png',     'alt_text' => 'レクサス',           'sort_order' => 100, 'is_main' => 1, 'is_active' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['manufacturer_id' => 2,  'image_type' => 'logo', 'file_path' => '/imgs/maker/maker_toyota.png',    'alt_text' => 'トヨタ',             'sort_order' => 110, 'is_main' => 1, 'is_active' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['manufacturer_id' => 3,  'image_type' => 'logo', 'file_path' => '/imgs/maker/maker_honda.png',     'alt_text' => 'ホンダ',             'sort_order' => 120, 'is_main' => 1, 'is_active' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['manufacturer_id' => 4,  'image_type' => 'logo', 'file_path' => '/imgs/maker/maker_nissan.png',    'alt_text' => '日産',               'sort_order' => 130, 'is_main' => 1, 'is_active' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['manufacturer_id' => 5,  'image_type' => 'logo', 'file_path' => '/imgs/maker/maker_suzuki.png',    'alt_text' => 'スズキ',             'sort_order' => 140, 'is_main' => 1, 'is_active' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['manufacturer_id' => 6,  'image_type' => 'logo', 'file_path' => '/imgs/maker/maker_daihatu.png',   'alt_text' => 'ダイハツ',           'sort_order' => 150, 'is_main' => 1, 'is_active' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['manufacturer_id' => 7,  'image_type' => 'logo', 'file_path' => '/imgs/maker/maker_mazda.png',     'alt_text' => 'マツダ',             'sort_order' => 160, 'is_main' => 1, 'is_active' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['manufacturer_id' => 8,  'image_type' => 'logo', 'file_path' => '/imgs/maker/maker_subaru.png',    'alt_text' => 'スバル',             'sort_order' => 170, 'is_main' => 1, 'is_active' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['manufacturer_id' => 9,  'image_type' => 'logo', 'file_path' => '/imgs/maker/maker_mitubishi.png', 'alt_text' => '三菱',               'sort_order' => 180, 'is_main' => 1, 'is_active' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['manufacturer_id' => 17, 'image_type' => 'logo', 'file_path' => '/imgs/maker/maker_mercedes.png',  'alt_text' => 'メルセデス・ベンツ', 'sort_order' => 200, 'is_main' => 1, 'is_active' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['manufacturer_id' => 23, 'image_type' => 'logo', 'file_path' => '/imgs/maker/maker_bmw.png',       'alt_text' => 'BMW',                'sort_order' => 210, 'is_main' => 1, 'is_active' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['manufacturer_id' => 26, 'image_type' => 'logo', 'file_path' => '/imgs/maker/maker_volkswagen.png','alt_text' => 'フォルクスワーゲン', 'sort_order' => 220, 'is_main' => 1, 'is_active' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['manufacturer_id' => 25, 'image_type' => 'logo', 'file_path' => '/imgs/maker/maker_audi.png',      'alt_text' => 'アウディ',           'sort_order' => 230, 'is_main' => 1, 'is_active' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['manufacturer_id' => 32, 'image_type' => 'logo', 'file_path' => '/imgs/maker/maker_mini.png',      'alt_text' => 'ミニ',               'sort_order' => 240, 'is_main' => 1, 'is_active' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['manufacturer_id' => 28, 'image_type' => 'logo', 'file_path' => '/imgs/maker/maker_porsche.png',   'alt_text' => 'ポルシェ',           'sort_order' => 250, 'is_main' => 1, 'is_active' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['manufacturer_id' => 92, 'image_type' => 'logo', 'file_path' => '/imgs/maker/maker_volvo.png',     'alt_text' => 'ボルボ',             'sort_order' => 260, 'is_main' => 1, 'is_active' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['manufacturer_id' => 96, 'image_type' => 'logo', 'file_path' => '/imgs/maker/maker_peugeot.png',   'alt_text' => 'プジョー',           'sort_order' => 270, 'is_main' => 1, 'is_active' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['manufacturer_id' => 69, 'image_type' => 'logo', 'file_path' => '/imgs/maker/maker_randrover.png', 'alt_text' => 'ランドローバー',     'sort_order' => 280, 'is_main' => 1, 'is_active' => 1, 'created_at' => $now, 'updated_at' => $now],
        ]);
    }
}