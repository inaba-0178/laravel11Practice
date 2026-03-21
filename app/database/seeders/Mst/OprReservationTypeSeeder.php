<?php

namespace Database\Seeders\Mst;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OprReservationTypeSeeder extends Seeder
{
    public function run(): void
    {
        DB::connection('mst')->table('opr_reservation_types')->truncate();

        $types = [
            [
                'name'        => '来店予約',
                'code'        => 'visit',
                'description' => '店舗に来店して車両をご確認いただけます。',
                'is_active'   => 1,
                'sort_order'  => 1,
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'name'        => '試乗予約',
                'code'        => 'test_drive',
                'description' => '実際に車両を試乗していただけます。',
                'is_active'   => 1,
                'sort_order'  => 2,
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'name'        => 'オンライン商談予約',
                'code'        => 'online',
                'description' => 'オンラインにて詳しくご説明いたします。',
                'is_active'   => 1,
                'sort_order'  => 3,
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
        ];

        DB::connection('mst')->table('opr_reservation_types')->insert($types);
    }
}