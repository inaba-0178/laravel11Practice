<?php

namespace Database\Seeders\Mst;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MstEquipmentEnvSeeder extends Seeder
{
    public function run(): void
    {
        $records = [
            ['value' => 'idling_stop', 'label' => 'アイドリングストップ',  'sort_order' => 1],
            ['value' => 'eco_car',     'label' => 'エコカー減税対象車',    'sort_order' => 2],
        ];

        foreach ($records as &$record) {
            $record['is_active']   = true;
            $record['created_at']  = now();
            $record['updated_at']  = now();
        }

        DB::connection('mst')->table('mst_equipment_env')->insert($records);
    }
}