<?php

namespace Database\Seeders\Mst;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MstEquipmentBasicSeeder extends Seeder
{
    public function run(): void
    {
        $records = [
            ['value' => 'keyless',        'label' => 'キーレスエントリー',       'sort_order' => 1],
            ['value' => 'etc',            'label' => 'ETC',                      'sort_order' => 2],
            ['value' => 'smart_key',      'label' => 'スマートキー',             'sort_order' => 3],
            ['value' => 'anti_theft',     'label' => '盗難防止装置',             'sort_order' => 4],
            ['value' => 'power_window',   'label' => 'パワーウィンドウ',         'sort_order' => 5],
            ['value' => 'sunroof',        'label' => 'サンルーフ・ガラスルーフ', 'sort_order' => 6],
            ['value' => 'power_seat',     'label' => 'パワステ',                 'sort_order' => 7],
            ['value' => 'roof_rail',      'label' => 'ルーフレール',             'sort_order' => 8],
            ['value' => 'ac',             'label' => 'エアコン・クーラー',       'sort_order' => 9],
            ['value' => 'rear_monitor',   'label' => '後席モニター',             'sort_order' => 10],
            ['value' => 'dual_ac',        'label' => 'Wエアコン',                'sort_order' => 11],
            ['value' => 'air_sus',        'label' => 'エアサスペンション',       'sort_order' => 12],
            ['value' => 'discharge',      'label' => 'ディスチャージヘッドランプ', 'sort_order' => 13],
            ['value' => 'power_1500',     'label' => '1500W給電',                'sort_order' => 14],
            ['value' => 'front_fog',      'label' => 'フロントフォグランプ',     'sort_order' => 15],
            ['value' => 'drive_rec',      'label' => 'ドライブレコーダー',       'sort_order' => 16],
            ['value' => 'auto_beam',      'label' => 'オートマチックハイビーム', 'sort_order' => 17],
            ['value' => 'power_back',     'label' => '電動開閉バックドア',       'sort_order' => 18],
            ['value' => 'led',            'label' => 'LEDヘッドライト',          'sort_order' => 19],
            ['value' => 'display_audio',  'label' => 'ディスプレイオーディオ',   'sort_order' => 20],
            ['value' => 'adaptive_head',  'label' => 'アダプティブヘッドライト', 'sort_order' => 21],
        ];

        foreach ($records as &$record) {
            $record['is_active']   = true;
            $record['created_at']  = now();
            $record['updated_at']  = now();
        }

        DB::connection('mst')->table('mst_equipment_basic')->insert($records);
    }
}