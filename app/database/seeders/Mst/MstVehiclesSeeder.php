<?php

namespace Database\Seeders\Mst;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MstVehiclesSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        // 有効データは CT 初期型・中期型・後期型の3件のみ
        // 残りのレクサス車両データは元SQLに構文エラー（series_idなし・sssss等）があるため未登録
        // データ整理後に追加してください
        DB::connection('mst')->table('mst_vehicles')->insert([
            [
                'series_id'      => 1,
                'manufacturer_id'=> 1,
                'name'           => 'CT 初期型 (2011-2013)',
                'model_code'     => 'ZWA10',
                'body_type'      => 10,
                'country_code'   => 'JP',
                'status'         => 'discontinued',
                'created_at'     => $now,
                'updated_at'     => $now,
                'deleted_at'     => null,
            ],
            [
                'series_id'      => 1,
                'manufacturer_id'=> 1,
                'name'           => 'CT 中期型 (2014-2017)',
                'model_code'     => 'ZWA10',
                'body_type'      => 10,
                'country_code'   => 'JP',
                'status'         => 'discontinued',
                'created_at'     => $now,
                'updated_at'     => $now,
                'deleted_at'     => null,
            ],
            [
                'series_id'      => 1,
                'manufacturer_id'=> 1,
                'name'           => 'CT 後期型 (2017-2022)',
                'model_code'     => 'ZWA10',
                'body_type'      => 10,
                'country_code'   => 'JP',
                'status'         => 'discontinued',
                'created_at'     => $now,
                'updated_at'     => $now,
                'deleted_at'     => null,
            ],
        ]);
    }
}