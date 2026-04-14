<?php

namespace Database\Seeders\Mst;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OprCarDealersSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        DB::connection('mst')->table('opr_car_dealers')->insert([
            [
                'name'             => 'テストディーラー',
                'postal_code'      => '123-4567',
                'region_id'        => 11,
                'city'             => 'テスト',
                'address_detail'   => 'テスト',
                'phone'            => '000-0000-0000',
                'email'            => '',
                'website_url'      => '',
                'business_hours'   => '',
                'regular_holiday'  => '',
                'dealer_type'      => 'used_car',
                'free_text'        => '',
                'is_active'        => 1,
                'created_at'       => $now,
                'updated_at'       => $now,
            ],
            [
                'name'             => 'テストディーラー2',
                'postal_code'      => '123-4567',
                'region_id'        => 11,
                'city'             => 'テスト2',
                'address_detail'   => 'テスト2',
                'phone'            => '000-0000-0000',
                'email'            => '',
                'website_url'      => '',
                'business_hours'   => '',
                'regular_holiday'  => '',
                'dealer_type'      => 'used_car',
                'free_text'        => '',
                'is_active'        => 1,
                'created_at'       => $now,
                'updated_at'       => $now,
            ],
            [
                'name'             => 'テストディーラー3',
                'postal_code'      => '123-4567',
                'region_id'        => 11,
                'city'             => 'テスト3',
                'address_detail'   => 'テスト3',
                'phone'            => '000-0000-0000',
                'email'            => '',
                'website_url'      => '',
                'business_hours'   => '',
                'regular_holiday'  => '',
                'dealer_type'      => 'used_car',
                'free_text'        => '',
                'is_active'        => 1,
                'created_at'       => $now,
                'updated_at'       => $now,
            ],
        ]);
    }
}