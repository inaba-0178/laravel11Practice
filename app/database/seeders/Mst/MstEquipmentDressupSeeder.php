<?php

namespace Database\Seeders\Mst;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MstEquipmentDressupSeeder extends Seeder
{
    public function run(): void
    {
        $records = [
            ['value' => 'full_aero',   'label' => 'フルエアロ',     'sort_order' => 1],
            ['value' => 'lowdown',     'label' => 'ローダウン',     'sort_order' => 2],
            ['value' => 'alloy_wheel', 'label' => 'アルミホイール', 'sort_order' => 3],
            ['value' => 'full_paint',  'label' => '全塗装済',       'sort_order' => 4],
            ['value' => 'lift_up',     'label' => 'リフトアップ',   'sort_order' => 5],
        ];

        foreach ($records as &$record) {
            $record['is_active']   = true;
            $record['created_at']  = now();
            $record['updated_at']  = now();
        }

        DB::connection('mst')->table('mst_equipment_dressup')->insert($records);
    }
}