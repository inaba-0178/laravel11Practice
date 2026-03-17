<?php

namespace Database\Seeders\Mst;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MstDetailOptionSeeder extends Seeder
{
    public function run(): void
    {
        $records = [
            ['value' => 'turbo',             'label' => '過給器設定モデル（ターボ・スーパーチャージャーなど）', 'sort_order' => 1],
            ['value' => 'unregistered2',     'label' => '登録（届出）済未使用車',                           'sort_order' => 2],
            ['value' => 'one_owner',         'label' => 'ワンオーナー',                                     'sort_order' => 3],
            ['value' => 'inspection_record', 'label' => '定期点検記録簿',                                   'sort_order' => 4],
            ['value' => 'non_smoking',       'label' => '禁煙車',                                           'sort_order' => 5],
            ['value' => 'display_test',      'label' => '展示・試乗車',                                     'sort_order' => 6],
            ['value' => 'rental_up',         'label' => 'レンタカーアップ',                                 'sort_order' => 7],
            ['value' => 'multi_photos',      'label' => '複数写真付き物件',                                 'sort_order' => 8],
        ];

        foreach ($records as &$record) {
            $record['is_active']   = true;
            $record['created_at']  = now();
            $record['updated_at']  = now();
        }

        DB::connection('mst')->table('mst_detail_options')->insert($records);
    }
}