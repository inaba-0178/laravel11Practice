<?php

namespace Database\Seeders\Mst;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MstEquipmentSafetySeeder extends Seeder
{
    public function run(): void
    {
        $records = [
            ['value' => 'abs',              'label' => 'ABS',                         'sort_order' => 1],
            ['value' => 'driver_airbag',    'label' => '運転席エアバッグ',             'sort_order' => 2],
            ['value' => 'support_car',      'label' => 'サポカー',                     'sort_order' => 3],
            ['value' => 'pass_airbag',      'label' => '助手席エアバッグ',             'sort_order' => 4],
            ['value' => 'collision_brake',  'label' => '衝突被害軽減ブレーキ',         'sort_order' => 5],
            ['value' => 'side_airbag',      'label' => 'サイドエアバッグ',             'sort_order' => 6],
            ['value' => 'cruise',           'label' => 'クルーズコントロール',         'sort_order' => 7],
            ['value' => 'curtain_airbag',   'label' => 'カーテンエアバッグ',           'sort_order' => 8],
            ['value' => 'adaptive_cruise',  'label' => 'アダプティブクルーズコントロール', 'sort_order' => 9],
            ['value' => 'knee_airbag',      'label' => '頭部衝撃緩和ヘッドレスト',    'sort_order' => 10],
            ['value' => 'lane_keep',        'label' => 'レーンキープアシスト',         'sort_order' => 11],
            ['value' => 'front_camera',     'label' => 'フロントカメラ',               'sort_order' => 12],
            ['value' => 'parking_assist',   'label' => 'パーキングアシスト',           'sort_order' => 13],
            ['value' => 'side_camera',      'label' => 'サイドカメラ',                 'sort_order' => 14],
            ['value' => 'no_accel_error',   'label' => 'アクセル踏み間違い防止装置',   'sort_order' => 15],
            ['value' => 'back_camera',      'label' => 'バックカメラ',                 'sort_order' => 16],
            ['value' => 'no_slip',          'label' => '横滑り防止装置',               'sort_order' => 17],
            ['value' => 'around_camera',    'label' => '全周囲カメラ',                 'sort_order' => 18],
            ['value' => 'obstacle',         'label' => '障害物センサー',               'sort_order' => 19],
            ['value' => 'blind_spot',       'label' => 'ブラインドスポットモニター',   'sort_order' => 20],
            ['value' => 'rear_traffic',     'label' => 'リアトラフィックモニタ',       'sort_order' => 21],
            ['value' => 'hill_descent',     'label' => 'ヒルディセントコントロール',   'sort_order' => 22],
        ];

        foreach ($records as &$record) {
            $record['is_active']   = true;
            $record['created_at']  = now();
            $record['updated_at']  = now();
        }

        DB::connection('mst')->table('mst_equipment_safety')->insert($records);
    }
}