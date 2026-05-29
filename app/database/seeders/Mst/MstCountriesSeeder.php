<?php

namespace Database\Seeders\Mst;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MstCountriesSeeder extends Seeder
{
    public function run(): void
    {
        $countries = [
            ['country_code' => 'JP', 'label' => '日本',         'flag' => 'JP', 'anchor' => 'japan',       'sort_order' => 100 ],
            ['country_code' => 'DE', 'label' => 'ドイツ',       'flag' => 'DE', 'anchor' => 'germany',     'sort_order' => 200 ],
            ['country_code' => 'US', 'label' => 'アメリカ',     'flag' => 'US', 'anchor' => 'us',          'sort_order' => 300 ],
            ['country_code' => 'CA', 'label' => 'カナダ',       'flag' => 'CA', 'anchor' => 'canada',      'sort_order' => 400 ],
            ['country_code' => 'GB', 'label' => 'イギリス',     'flag' => 'GB', 'anchor' => 'uk',          'sort_order' => 500 ],
            ['country_code' => 'IT', 'label' => 'イタリア',     'flag' => 'IT', 'anchor' => 'italy',       'sort_order' => 600 ],
            ['country_code' => 'FR', 'label' => 'フランス',     'flag' => 'FR', 'anchor' => 'france',      'sort_order' => 700 ],
            ['country_code' => 'SE', 'label' => 'スウェーデン', 'flag' => 'SE', 'anchor' => 'sweden',      'sort_order' => 800 ],
            ['country_code' => 'KR', 'label' => '韓国',         'flag' => 'KR', 'anchor' => 'korea',       'sort_order' => 900 ],
            ['country_code' => 'CN', 'label' => '中国',         'flag' => 'CN', 'anchor' => 'china',       'sort_order' => 1000],
            ['country_code' => 'AT', 'label' => 'オーストリア', 'flag' => 'AT', 'anchor' => 'austria',     'sort_order' => 1100],
            ['country_code' => 'ES', 'label' => 'スペイン',     'flag' => 'ES', 'anchor' => 'spain',       'sort_order' => 1200],
            ['country_code' => 'SI', 'label' => 'スロベニア',   'flag' => 'SI', 'anchor' => 'slovenia',    'sort_order' => 1300],
            ['country_code' => 'RU', 'label' => 'ロシア',       'flag' => 'RU', 'anchor' => 'russia',      'sort_order' => 1400],
            ['country_code' => 'MY', 'label' => 'マレーシア',   'flag' => 'MY', 'anchor' => 'malaysia',    'sort_order' => 1500],
            ['country_code' => 'ZA', 'label' => '南アフリカ',   'flag' => 'ZA', 'anchor' => 'southafrica', 'sort_order' => 1600],
            ['country_code' => 'XX', 'label' => 'その他輸入車', 'flag' => 'OTH',   'anchor' => 'others',      'sort_order' => 9999],
        ];

        $now = now();

        DB::connection('mst')->table('mst_countries')->insert(
            array_map(fn($row) => array_merge($row, [
                'created_at' => $now,
                'updated_at' => $now,
            ]), $countries)
        );
    }
}