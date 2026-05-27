<?php

namespace Database\Seeders\Opr;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OprMainViewSeeder extends Seeder
{
    public function run(): void
    {
        DB::connection('mst')->table('opr_main_views')->insert([
            [
                'title'      => 'あなたの一台を、ここで。',
                'sub'        => '全国の厳選された中古車をお探しいただけます。',
                'label'      => 'PREMIUM CAR SEARCH',
                'image_path' => null,
                'link_url'   => null,
                'sort_order' => 100,
                'is_active'  => 1,
                'start_at'   => null,
                'end_at'     => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title'      => 'プレミアムカーを探す',
                'sub'        => '高級車・輸入車・スポーツカーなど豊富なラインナップ。',
                'label'      => 'PREMIUM CAR SEARCH',
                'image_path' => null,
                'link_url'   => null,
                'sort_order' => 200,
                'is_active'  => 1,
                'start_at'   => null,
                'end_at'     => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title'      => '理想のカーライフを実現',
                'sub'        => 'メーカー・ボディタイプ・エリアから簡単検索。',
                'label'      => 'PREMIUM CAR SEARCH',
                'image_path' => null,
                'link_url'   => null,
                'sort_order' => 300,
                'is_active'  => 1,
                'start_at'   => null,
                'end_at'     => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}