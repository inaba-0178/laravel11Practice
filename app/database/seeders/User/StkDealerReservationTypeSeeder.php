<?php

namespace Database\Seeders\User;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StkDealerReservationTypeSeeder extends Seeder
{
    public function run(): void
    {
        DB::connection('user')->table('stk_dealer_reservation_types')->truncate();

        // dealer_id: 1 は来店・オンライン商談のみ許可（試乗は不可）
        $data = [
            [
                'dealer_id'           => 1,
                'reservation_type_id' => 1, // 来店予約
                'is_active'           => 1,
                'created_at'          => now(),
                'updated_at'          => now(),
            ],
            [
                'dealer_id'           => 1,
                'reservation_type_id' => 2, // 試乗予約
                'is_active'           => 0, // 不許可
                'created_at'          => now(),
                'updated_at'          => now(),
            ],
            [
                'dealer_id'           => 1,
                'reservation_type_id' => 3, // オンライン商談
                'is_active'           => 1,
                'created_at'          => now(),
                'updated_at'          => now(),
            ],
        ];

        DB::connection('user')->table('stk_dealer_reservation_types')->insert($data);
    }
}