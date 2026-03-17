<?php

namespace Database\Seeders\Mst;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MstSeatOptionSeeder extends Seeder
{
    public function run(): void
    {
        $records = [
            ['value' => 'flat_seat',    'label' => 'フルフラットシート', 'sort_order' => 1],
            ['value' => 'leather_seat', 'label' => '本革シート',         'sort_order' => 2],
            ['value' => '3row_seat',    'label' => '3列シート',          'sort_order' => 3],
            ['value' => 'bench_seat',   'label' => 'ベンチシート',       'sort_order' => 4],
            ['value' => 'walkthrough',  'label' => 'ウォークスルー',     'sort_order' => 5],
            ['value' => 'electric_seat','label' => '電動シート',         'sort_order' => 6],
            ['value' => 'seat_heater',  'label' => 'シートヒーター',     'sort_order' => 7],
            ['value' => 'ottoman',      'label' => 'オットマン',         'sort_order' => 8],
            ['value' => 'seat_ac',      'label' => 'シートエアコン',     'sort_order' => 9],
        ];

        foreach ($records as &$record) {
            $record['is_active']   = true;
            $record['created_at']  = now();
            $record['updated_at']  = now();
        }

        DB::connection('mst')->table('mst_seat_options')->insert($records);
    }
}