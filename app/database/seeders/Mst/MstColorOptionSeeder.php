<?php

namespace Database\Seeders\Mst;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MstColorOptionSeeder extends Seeder
{
    public function run(): void
    {
        $records = [
            ['value' => 'white',        'label' => 'ホワイト',       'hex_code' => '#ffffff', 'sort_order' => 1],
            ['value' => 'light-cream',  'label' => 'クリーム',       'hex_code' => '#e8e0c8', 'sort_order' => 2],
            ['value' => 'black',        'label' => 'ブラック',       'hex_code' => '#1a1a1a', 'sort_order' => 3],
            ['value' => 'red',          'label' => 'レッド',         'hex_code' => '#cc2222', 'sort_order' => 4],
            ['value' => 'silver',       'label' => 'シルバー',       'hex_code' => '#aaaaaa', 'sort_order' => 5],
            ['value' => 'blue',         'label' => 'ブルー',         'hex_code' => '#2244cc', 'sort_order' => 6],
            ['value' => 'light-silver', 'label' => 'ライトシルバー', 'hex_code' => '#cccccc', 'sort_order' => 7],
            ['value' => 'gold',         'label' => 'ゴールド',       'hex_code' => '#c8a030', 'sort_order' => 8],
            ['value' => 'yellow',       'label' => 'イエロー',       'hex_code' => '#d4aa00', 'sort_order' => 9],
            ['value' => 'purple',       'label' => 'パープル',       'hex_code' => '#662288', 'sort_order' => 10],
            ['value' => 'green',        'label' => 'グリーン',       'hex_code' => '#22aa22', 'sort_order' => 11],
            ['value' => 'pink',         'label' => 'ピンク',         'hex_code' => '#f5c0c8', 'sort_order' => 12],
            ['value' => 'rose',         'label' => 'ローズ',         'hex_code' => '#e8a0a8', 'sort_order' => 13],
            ['value' => 'gray-pearl',   'label' => 'グレーパール',   'hex_code' => '#c8c8c0', 'sort_order' => 14],
        ];

        foreach ($records as &$record) {
            $record['is_active']   = true;
            $record['created_at']  = now();
            $record['updated_at']  = now();
        }

        DB::connection('mst')->table('mst_color_options')->insert($records);
    }
}