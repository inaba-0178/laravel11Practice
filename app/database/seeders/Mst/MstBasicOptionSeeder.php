<?php

namespace Database\Seeders\Mst;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MstBasicOptionSeeder extends Seeder
{
    public function run(): void
    {
        $records = [
            ['value' => 'new_arrival',    'label' => '新着物件',               'is_highlight' => true,  'sort_order' => 1],
            ['value' => 'unregistered',   'label' => '未登録車',               'is_highlight' => false, 'sort_order' => 2],
            ['value' => 'total_payment',  'label' => '支払総額あり',           'is_highlight' => false, 'sort_order' => 3],
            ['value' => 'maker_dealer',   'label' => 'メーカー系販売店',       'is_highlight' => false, 'sort_order' => 4],
            ['value' => 'no_repair',      'label' => '修復歴なし',             'is_highlight' => false, 'sort_order' => 5],
            ['value' => 'coupon',         'label' => 'クーポン付き',           'is_highlight' => false, 'sort_order' => 6],
            ['value' => 'quality_cert',   'label' => '車両品質評価書付き',     'is_highlight' => false, 'sort_order' => 7],
            ['value' => 'purchase_plan',  'label' => '購入プラン付き',         'is_highlight' => false, 'sort_order' => 8],
            ['value' => 'sensor_after',   'label' => 'カーセンサーアフター保証対象車', 'is_highlight' => false, 'sort_order' => 9],
            ['value' => '360_image',      'label' => '360°画像付き車両',      'is_highlight' => false, 'sort_order' => 10],
            ['value' => 'online_consult', 'label' => 'オンライン相談可',       'is_highlight' => false, 'sort_order' => 11],
        ];

        foreach ($records as &$record) {
            $record['is_active']   = true;
            $record['created_at']  = now();
            $record['updated_at']  = now();
        }

        DB::connection('mst')->table('mst_basic_options')->insert($records);
    }
}