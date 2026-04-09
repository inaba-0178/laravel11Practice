<?php

namespace Database\Seeders\User;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StkDealerFeeSeeder extends Seeder
{
    public function run(): void
    {
        $dealers = DB::connection('user')->table('stk_car_dealers')->pluck('id');

        $now = now();
        $records = [];

        foreach ($dealers as $dealerId) {
            $records[] = [
                'dealer_id'        => $dealerId,
                'name'             => 'standard',
                'is_default'       => true,
                'registration_fee' => 30000,
                'garage_cert_fee'  => 15000,
                'delivery_fee'     => 20000,
                'maintenance_fee'  => 50000,
                'created_at'       => $now,
                'updated_at'       => $now,
            ];
        }

        DB::connection('user')->table('stk_dealer_fees')->insert($records);
    }
}