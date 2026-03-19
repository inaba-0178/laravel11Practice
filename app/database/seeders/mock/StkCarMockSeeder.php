<?php

namespace Database\Seeders\mock;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StkCarMockSeeder extends Seeder
{
    public function run(): void
    {
        // stk_car_details
        DB::connection('user')->table('stk_car_details')->truncate();

        $details = [
            [
                'car_id'                  => 25,
                'first_registration_date' => '2020-04-01',
                'inspection_expire_date'  => '2026-04-30',
                'inspection_status'       => 'available',
                'drive_system'            => '4WD',
                'displacement'            => 2000,
                'steering_wheel'          => 'right',
                'number_of_doors'         => 5,
                'slide_door'              => 'none',
                'riding_capacity'         => 5,
                'loan_available'          => 1,
                'description'             => 'テスト車両です。',
                'free_text'               => 'SUV 4WD 2000cc',
                'seo_title'               => null,
                'seo_description'         => null,
            ],
            [
                'car_id'                  => 26,
                'first_registration_date' => '2019-07-01',
                'inspection_expire_date'  => '2025-07-31',
                'inspection_status'       => 'available',
                'drive_system'            => '2WD',
                'displacement'            => 1800,
                'steering_wheel'          => 'right',
                'number_of_doors'         => 4,
                'slide_door'              => 'none',
                'riding_capacity'         => 5,
                'loan_available'          => 1,
                'description'             => 'テスト車両です。',
                'free_text'               => 'セダン 2WD 1800cc',
                'seo_title'               => null,
                'seo_description'         => null,
            ],
            [
                'car_id'                  => 27,
                'first_registration_date' => '2021-01-01',
                'inspection_expire_date'  => '2027-01-31',
                'inspection_status'       => 'available',
                'drive_system'            => 'AWD',
                'displacement'            => 2500,
                'steering_wheel'          => 'right',
                'number_of_doors'         => 5,
                'slide_door'              => 'both_power',
                'riding_capacity'         => 7,
                'loan_available'          => 1,
                'description'             => 'テスト車両です。',
                'free_text'               => 'ミニバン AWD 2500cc',
                'seo_title'               => null,
                'seo_description'         => null,
            ],
            [
                'car_id'                  => 28,
                'first_registration_date' => '2018-10-01',
                'inspection_expire_date'  => null,
                'inspection_status'       => 'none',
                'drive_system'            => 'FF',
                'displacement'            => 1500,
                'steering_wheel'          => 'right',
                'number_of_doors'         => 4,
                'slide_door'              => 'none',
                'riding_capacity'         => 5,
                'loan_available'          => 0,
                'description'             => 'テスト車両です。',
                'free_text'               => 'コンパクト FF 1500cc',
                'seo_title'               => null,
                'seo_description'         => null,
            ],
            [
                'car_id'                  => 29,
                'first_registration_date' => '2022-03-01',
                'inspection_expire_date'  => '2026-03-31',
                'inspection_status'       => 'available',
                'drive_system'            => 'FR',
                'displacement'            => 3000,
                'steering_wheel'          => 'right',
                'number_of_doors'         => 2,
                'slide_door'              => 'none',
                'riding_capacity'         => 4,
                'loan_available'          => 1,
                'description'             => 'テスト車両です。',
                'free_text'               => 'クーペ FR 3000cc',
                'seo_title'               => null,
                'seo_description'         => null,
            ],
        ];

        foreach ($details as &$detail) {
            $detail['created_at'] = now();
            $detail['updated_at'] = now();
        }

        DB::connection('user')->table('stk_car_details')->insert($details);

        // stk_car_options
        DB::connection('user')->table('stk_car_options')->truncate();

        $options = [];
        $carIds  = [25, 26, 27, 28, 29];

        $basicOptions = [
            ['category' => 'basic', 'name' => 'keyless',      'label' => 'キーレスエントリー'],
            ['category' => 'basic', 'name' => 'etc',          'label' => 'ETC'],
            ['category' => 'basic', 'name' => 'smart_key',    'label' => 'スマートキー'],
            ['category' => 'basic', 'name' => 'power_window', 'label' => 'パワーウィンドウ'],
            ['category' => 'basic', 'name' => 'led',          'label' => 'LEDヘッドライト'],
        ];

        $safetyOptions = [
            ['category' => 'safety', 'name' => 'back_camera',     'label' => 'バックカメラ'],
            ['category' => 'safety', 'name' => 'collision_brake',  'label' => '衝突被害軽減ブレーキ'],
            ['category' => 'safety', 'name' => 'driver_airbag',    'label' => '運転席エアバッグ'],
        ];

        $specialOptions = [
            ['category' => 'special_type', 'name' => 'reimport',    'label' => '逆輸入車'],
        ];

        $order = 1;
        foreach ($carIds as $carId) {
            foreach ($basicOptions as $opt) {
                $options[] = [
                    'car_id'          => $carId,
                    'option_category' => $opt['category'],
                    'option_name'     => $opt['name'],
                    'is_equipped'     => 1,
                    'display_order'   => $order++,
                    'created_at'      => now(),
                    'updated_at'      => now(),
                ];
            }
            foreach ($safetyOptions as $opt) {
                $options[] = [
                    'car_id'          => $carId,
                    'option_category' => $opt['category'],
                    'option_name'     => $opt['name'],
                    'is_equipped'     => 1,
                    'display_order'   => $order++,
                    'created_at'      => now(),
                    'updated_at'      => now(),
                ];
            }
        }

        // car_id 29だけ逆輸入車フラグ
        $options[] = [
            'car_id'          => 29,
            'option_category' => 'special_type',
            'option_name'     => 'reimport',
            'is_equipped'     => 1,
            'display_order'   => $order++,
            'created_at'      => now(),
            'updated_at'      => now(),
        ];

        DB::connection('user')->table('stk_car_options')->insert($options);
    }
}