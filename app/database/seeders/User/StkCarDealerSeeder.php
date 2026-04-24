<?php

namespace Database\Seeders\User;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StkCarDealerSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        $dealers = [
            // 埼玉県（area_code:3, region_id:11）50件
            ['name' => 'ネクステージ 草加店',       'city' => '草加市',     'address_detail' => '栄町1-1-1',       'phone' => '048-001-0001', 'area_code' => 3, 'region_id' => 11],
            ['name' => 'ネクステージ 川口店',       'city' => '川口市',     'address_detail' => '並木2-2-2',       'phone' => '048-001-0002', 'area_code' => 3, 'region_id' => 11],
            ['name' => 'ネクステージ さいたま店',   'city' => 'さいたま市', 'address_detail' => '大宮区3-3-3',     'phone' => '048-001-0003', 'area_code' => 3, 'region_id' => 11],
            ['name' => 'ネクステージ 所沢店',       'city' => '所沢市',     'address_detail' => '元町4-4-4',       'phone' => '048-001-0004', 'area_code' => 3, 'region_id' => 11],
            ['name' => 'ネクステージ 越谷店',       'city' => '越谷市',     'address_detail' => '南越谷5-5-5',     'phone' => '048-001-0005', 'area_code' => 3, 'region_id' => 11],
            ['name' => 'ネクステージ 春日部店',     'city' => '春日部市',   'address_detail' => '中央6-6-6',       'phone' => '048-001-0006', 'area_code' => 3, 'region_id' => 11],
            ['name' => 'ネクステージ 熊谷店',       'city' => '熊谷市',     'address_detail' => '銀座7-7-7',       'phone' => '048-001-0007', 'area_code' => 3, 'region_id' => 11],
            ['name' => 'ネクステージ 上尾店',       'city' => '上尾市',     'address_detail' => '本町8-8-8',       'phone' => '048-001-0008', 'area_code' => 3, 'region_id' => 11],
            ['name' => 'ネクステージ 狭山店',       'city' => '狭山市',     'address_detail' => '入間川9-9-9',     'phone' => '048-001-0009', 'area_code' => 3, 'region_id' => 11],
            ['name' => 'ネクステージ 入間店',       'city' => '入間市',     'address_detail' => '豊岡10-10-10',    'phone' => '048-001-0010', 'area_code' => 3, 'region_id' => 11],
            ['name' => 'オートセンター 浦和店',     'city' => 'さいたま市', 'address_detail' => '浦和区1-1-1',     'phone' => '048-002-0001', 'area_code' => 3, 'region_id' => 11],
            ['name' => 'オートセンター 大宮店',     'city' => 'さいたま市', 'address_detail' => '大宮区2-2-2',     'phone' => '048-002-0002', 'area_code' => 3, 'region_id' => 11],
            ['name' => 'オートセンター 川越店',     'city' => '川越市',     'address_detail' => '脇田町3-3-3',     'phone' => '048-002-0003', 'area_code' => 3, 'region_id' => 11],
            ['name' => 'オートセンター 蕨店',       'city' => '蕨市',       'address_detail' => '錦町4-4-4',       'phone' => '048-002-0004', 'area_code' => 3, 'region_id' => 11],
            ['name' => 'オートセンター 戸田店',     'city' => '戸田市',     'address_detail' => '上戸田5-5-5',     'phone' => '048-002-0005', 'area_code' => 3, 'region_id' => 11],
            ['name' => 'オートセンター 朝霞店',     'city' => '朝霞市',     'address_detail' => '本町6-6-6',       'phone' => '048-002-0006', 'area_code' => 3, 'region_id' => 11],
            ['name' => 'オートセンター 新座店',     'city' => '新座市',     'address_detail' => '野火止7-7-7',     'phone' => '048-002-0007', 'area_code' => 3, 'region_id' => 11],
            ['name' => 'オートセンター 和光店',     'city' => '和光市',     'address_detail' => '広沢8-8-8',       'phone' => '048-002-0008', 'area_code' => 3, 'region_id' => 11],
            ['name' => 'オートセンター 志木店',     'city' => '志木市',     'address_detail' => '本町9-9-9',       'phone' => '048-002-0009', 'area_code' => 3, 'region_id' => 11],
            ['name' => 'オートセンター 富士見店',   'city' => '富士見市',   'address_detail' => '鶴馬10-10-10',    'phone' => '048-002-0010', 'area_code' => 3, 'region_id' => 11],
            ['name' => 'カーランド 草加店',         'city' => '草加市',     'address_detail' => '谷塚1-1-1',       'phone' => '048-003-0001', 'area_code' => 3, 'region_id' => 11],
            ['name' => 'カーランド 三郷店',         'city' => '三郷市',     'address_detail' => '中央2-2-2',       'phone' => '048-003-0002', 'area_code' => 3, 'region_id' => 11],
            ['name' => 'カーランド 八潮店',         'city' => '八潮市',     'address_detail' => '中央3-3-3',       'phone' => '048-003-0003', 'area_code' => 3, 'region_id' => 11],
            ['name' => 'カーランド 吉川店',         'city' => '吉川市',     'address_detail' => '中央4-4-4',       'phone' => '048-003-0004', 'area_code' => 3, 'region_id' => 11],
            ['name' => 'カーランド 松伏店',         'city' => '松伏町',     'address_detail' => '松伏5-5-5',       'phone' => '048-003-0005', 'area_code' => 3, 'region_id' => 11],
            ['name' => 'カーランド 宮代店',         'city' => '宮代町',     'address_detail' => '百間6-6-6',       'phone' => '048-003-0006', 'area_code' => 3, 'region_id' => 11],
            ['name' => 'カーランド 杉戸店',         'city' => '杉戸町',     'address_detail' => '杉戸7-7-7',       'phone' => '048-003-0007', 'area_code' => 3, 'region_id' => 11],
            ['name' => 'カーランド 幸手店',         'city' => '幸手市',     'address_detail' => '中央8-8-8',       'phone' => '048-003-0008', 'area_code' => 3, 'region_id' => 11],
            ['name' => 'カーランド 久喜店',         'city' => '久喜市',     'address_detail' => '久喜9-9-9',       'phone' => '048-003-0009', 'area_code' => 3, 'region_id' => 11],
            ['name' => 'カーランド 蓮田店',         'city' => '蓮田市',     'address_detail' => '蓮田10-10-10',    'phone' => '048-003-0010', 'area_code' => 3, 'region_id' => 11],
            ['name' => 'ユーカーパーク 大宮店',     'city' => 'さいたま市', 'address_detail' => '北区1-1-1',       'phone' => '048-004-0001', 'area_code' => 3, 'region_id' => 11],
            ['name' => 'ユーカーパーク 浦和店',     'city' => 'さいたま市', 'address_detail' => '浦和区2-2-2',     'phone' => '048-004-0002', 'area_code' => 3, 'region_id' => 11],
            ['name' => 'ユーカーパーク 川口店',     'city' => '川口市',     'address_detail' => '青木3-3-3',       'phone' => '048-004-0003', 'area_code' => 3, 'region_id' => 11],
            ['name' => 'ユーカーパーク 所沢店',     'city' => '所沢市',     'address_detail' => '西所沢4-4-4',     'phone' => '048-004-0004', 'area_code' => 3, 'region_id' => 11],
            ['name' => 'ユーカーパーク 越谷店',     'city' => '越谷市',     'address_detail' => '大沢5-5-5',       'phone' => '048-004-0005', 'area_code' => 3, 'region_id' => 11],
            ['name' => 'ユーカーパーク 春日部店',   'city' => '春日部市',   'address_detail' => '粕壁6-6-6',       'phone' => '048-004-0006', 'area_code' => 3, 'region_id' => 11],
            ['name' => 'ユーカーパーク 熊谷店',     'city' => '熊谷市',     'address_detail' => '本町7-7-7',       'phone' => '048-004-0007', 'area_code' => 3, 'region_id' => 11],
            ['name' => 'ユーカーパーク 行田店',     'city' => '行田市',     'address_detail' => '本丸8-8-8',       'phone' => '048-004-0008', 'area_code' => 3, 'region_id' => 11],
            ['name' => 'ユーカーパーク 鴻巣店',     'city' => '鴻巣市',     'address_detail' => '本町9-9-9',       'phone' => '048-004-0009', 'area_code' => 3, 'region_id' => 11],
            ['name' => 'ユーカーパーク 北本店',     'city' => '北本市',     'address_detail' => '北本10-10-10',    'phone' => '048-004-0010', 'area_code' => 3, 'region_id' => 11],
            ['name' => 'SUVランド 大宮店',          'city' => 'さいたま市', 'address_detail' => '西区1-1-1',       'phone' => '048-005-0001', 'area_code' => 3, 'region_id' => 11],
            ['name' => 'SUVランド 川越店',          'city' => '川越市',     'address_detail' => '新宿町2-2-2',     'phone' => '048-005-0002', 'area_code' => 3, 'region_id' => 11],
            ['name' => 'SUVランド 狭山店',          'city' => '狭山市',     'address_detail' => '新狭山3-3-3',     'phone' => '048-005-0003', 'area_code' => 3, 'region_id' => 11],
            ['name' => 'SUVランド 飯能店',          'city' => '飯能市',     'address_detail' => '本町4-4-4',       'phone' => '048-005-0004', 'area_code' => 3, 'region_id' => 11],
            ['name' => 'SUVランド 日高店',          'city' => '日高市',     'address_detail' => '高萩5-5-5',       'phone' => '048-005-0005', 'area_code' => 3, 'region_id' => 11],
            ['name' => 'SUVランド 毛呂山店',        'city' => '毛呂山町',   'address_detail' => '毛呂本郷6-6-6',   'phone' => '048-005-0006', 'area_code' => 3, 'region_id' => 11],
            ['name' => 'SUVランド 越生店',          'city' => '越生町',     'address_detail' => '越生7-7-7',       'phone' => '048-005-0007', 'area_code' => 3, 'region_id' => 11],
            ['name' => 'SUVランド 坂戸店',          'city' => '坂戸市',     'address_detail' => '日の出町8-8-8',   'phone' => '048-005-0008', 'area_code' => 3, 'region_id' => 11],
            ['name' => 'SUVランド 鶴ヶ島店',        'city' => '鶴ヶ島市',   'address_detail' => '三ツ木9-9-9',     'phone' => '048-005-0009', 'area_code' => 3, 'region_id' => 11],
            ['name' => 'SUVランド ふじみ野店',      'city' => 'ふじみ野市', 'address_detail' => '上福岡10-10-10',  'phone' => '048-005-0010', 'area_code' => 3, 'region_id' => 11],

            // 東京都（area_code:3, region_id:13）20件
            ['name' => 'ネクステージ 新宿店',       'city' => '新宿区',     'address_detail' => '西新宿1-1-1',     'phone' => '03-001-0001', 'area_code' => 3, 'region_id' => 13],
            ['name' => 'ネクステージ 渋谷店',       'city' => '渋谷区',     'address_detail' => '道玄坂2-2-2',     'phone' => '03-001-0002', 'area_code' => 3, 'region_id' => 13],
            ['name' => 'ネクステージ 池袋店',       'city' => '豊島区',     'address_detail' => '西池袋3-3-3',     'phone' => '03-001-0003', 'area_code' => 3, 'region_id' => 13],
            ['name' => 'ネクステージ 立川店',       'city' => '立川市',     'address_detail' => '錦町4-4-4',       'phone' => '042-001-0004', 'area_code' => 3, 'region_id' => 13],
            ['name' => 'ネクステージ 八王子店',     'city' => '八王子市',   'address_detail' => '旭町5-5-5',       'phone' => '042-001-0005', 'area_code' => 3, 'region_id' => 13],
            ['name' => 'オートセンター 世田谷店',   'city' => '世田谷区',   'address_detail' => '三軒茶屋1-1-1',   'phone' => '03-002-0001', 'area_code' => 3, 'region_id' => 13],
            ['name' => 'オートセンター 江東店',     'city' => '江東区',     'address_detail' => '亀戸2-2-2',       'phone' => '03-002-0002', 'area_code' => 3, 'region_id' => 13],
            ['name' => 'オートセンター 足立店',     'city' => '足立区',     'address_detail' => '千住3-3-3',       'phone' => '03-002-0003', 'area_code' => 3, 'region_id' => 13],
            ['name' => 'オートセンター 葛飾店',     'city' => '葛飾区',     'address_detail' => '亀有4-4-4',       'phone' => '03-002-0004', 'area_code' => 3, 'region_id' => 13],
            ['name' => 'オートセンター 江戸川店',   'city' => '江戸川区',   'address_detail' => '小岩5-5-5',       'phone' => '03-002-0005', 'area_code' => 3, 'region_id' => 13],
            ['name' => 'カーランド 町田店',         'city' => '町田市',     'address_detail' => '原町田1-1-1',     'phone' => '042-003-0001', 'area_code' => 3, 'region_id' => 13],
            ['name' => 'カーランド 府中店',         'city' => '府中市',     'address_detail' => '宮西町2-2-2',     'phone' => '042-003-0002', 'area_code' => 3, 'region_id' => 13],
            ['name' => 'カーランド 調布店',         'city' => '調布市',     'address_detail' => '布田3-3-3',       'phone' => '042-003-0003', 'area_code' => 3, 'region_id' => 13],
            ['name' => 'カーランド 三鷹店',         'city' => '三鷹市',     'address_detail' => '下連雀4-4-4',     'phone' => '0422-003-0004', 'area_code' => 3, 'region_id' => 13],
            ['name' => 'カーランド 武蔵野店',       'city' => '武蔵野市',   'address_detail' => '吉祥寺5-5-5',     'phone' => '0422-003-0005', 'area_code' => 3, 'region_id' => 13],
            ['name' => 'SUVランド 青梅店',          'city' => '青梅市',     'address_detail' => '本町1-1-1',       'phone' => '0428-004-0001', 'area_code' => 3, 'region_id' => 13],
            ['name' => 'SUVランド 昭島店',          'city' => '昭島市',     'address_detail' => '中神町2-2-2',     'phone' => '042-004-0002', 'area_code' => 3, 'region_id' => 13],
            ['name' => 'SUVランド 小平店',          'city' => '小平市',     'address_detail' => '花小金井3-3-3',   'phone' => '042-004-0003', 'area_code' => 3, 'region_id' => 13],
            ['name' => 'SUVランド 東村山店',        'city' => '東村山市',   'address_detail' => '本町4-4-4',       'phone' => '042-004-0004', 'area_code' => 3, 'region_id' => 13],
            ['name' => 'SUVランド 東大和店',        'city' => '東大和市',   'address_detail' => '中央5-5-5',       'phone' => '042-004-0005', 'area_code' => 3, 'region_id' => 13],

            // 神奈川県（area_code:3, region_id:14）15件
            ['name' => 'ネクステージ 横浜店',       'city' => '横浜市',     'address_detail' => '西区1-1-1',       'phone' => '045-001-0001', 'area_code' => 3, 'region_id' => 14],
            ['name' => 'ネクステージ 川崎店',       'city' => '川崎市',     'address_detail' => '川崎区2-2-2',     'phone' => '044-001-0002', 'area_code' => 3, 'region_id' => 14],
            ['name' => 'ネクステージ 相模原店',     'city' => '相模原市',   'address_detail' => '中央区3-3-3',     'phone' => '042-001-0003', 'area_code' => 3, 'region_id' => 14],
            ['name' => 'オートセンター 藤沢店',     'city' => '藤沢市',     'address_detail' => '南藤沢1-1-1',     'phone' => '0466-002-0001', 'area_code' => 3, 'region_id' => 14],
            ['name' => 'オートセンター 平塚店',     'city' => '平塚市',     'address_detail' => '紅谷町2-2-2',     'phone' => '0463-002-0002', 'area_code' => 3, 'region_id' => 14],
            ['name' => 'カーランド 厚木店',         'city' => '厚木市',     'address_detail' => '中町1-1-1',       'phone' => '046-003-0001', 'area_code' => 3, 'region_id' => 14],
            ['name' => 'カーランド 小田原店',       'city' => '小田原市',   'address_detail' => '栄町2-2-2',       'phone' => '0465-003-0002', 'area_code' => 3, 'region_id' => 14],
            ['name' => 'カーランド 茅ヶ崎店',       'city' => '茅ヶ崎市',   'address_detail' => '幸町3-3-3',       'phone' => '0467-003-0003', 'area_code' => 3, 'region_id' => 14],
            ['name' => 'SUVランド 横須賀店',        'city' => '横須賀市',   'address_detail' => '若松町1-1-1',     'phone' => '046-004-0001', 'area_code' => 3, 'region_id' => 14],
            ['name' => 'SUVランド 鎌倉店',          'city' => '鎌倉市',     'address_detail' => '御成町2-2-2',     'phone' => '0467-004-0002', 'area_code' => 3, 'region_id' => 14],
            ['name' => 'ユーカーパーク 横浜店',     'city' => '横浜市',     'address_detail' => '港北区3-3-3',     'phone' => '045-005-0003', 'area_code' => 3, 'region_id' => 14],
            ['name' => 'ユーカーパーク 川崎店',     'city' => '川崎市',     'address_detail' => '幸区4-4-4',       'phone' => '044-005-0004', 'area_code' => 3, 'region_id' => 14],
            ['name' => 'ユーカーパーク 相模原店',   'city' => '相模原市',   'address_detail' => '緑区5-5-5',       'phone' => '042-005-0005', 'area_code' => 3, 'region_id' => 14],
            ['name' => 'ユーカーパーク 大和店',     'city' => '大和市',     'address_detail' => '中央6-6-6',       'phone' => '046-005-0006', 'area_code' => 3, 'region_id' => 14],
            ['name' => 'ユーカーパーク 海老名店',   'city' => '海老名市',   'address_detail' => '中央7-7-7',       'phone' => '046-005-0007', 'area_code' => 3, 'region_id' => 14],
        ];

        foreach ($dealers as $dealer) {
            DB::connection('user')->table('stk_car_dealers')->insert([
                'name'                          => $dealer['name'],
                'postal_code'                   => '000-0000',
                'region_id'                     => $dealer['region_id'],
                'area_code'                     => $dealer['area_code'],
                'city'                          => $dealer['city'],
                'address_detail'                => $dealer['address_detail'],
                'phone'                         => $dealer['phone'],
                'email'                         => null,
                'website_url'                   => null,
                'business_hours_from'           => '10:00',
                'business_hours_to'             => '19:00',
                'regular_holiday_days'          => '火曜',
                'regular_holiday_except_holiday'=> 0,
                'dealer_type'                   => 'used_car',
                'free_text'                     => null,
                'latitude'                      => null,
                'longitude'                     => null,
                'is_active'                     => 1,
                'review_rating'                 => null,
                'review_count'                  => 0,
                'loan_setting_enabled'          => 0,
                'created_at'                    => $now,
                'updated_at'                    => $now,
            ]);
        }
    }
}