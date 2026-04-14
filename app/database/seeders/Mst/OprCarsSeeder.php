<?php

namespace Database\Seeders\Mst;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OprCarsSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        DB::connection('mst')->table('opr_cars')->insert([
            [
                'dealer_id'          => 1,
                'series_id'          => 1,
                'vehicle_id'         => 1,
                'stock_number'       => 1,
                'status'             => 'available',
                'price'              => 4000000,
                'price_display_type' => 'actual',
                'model_year'         => '2020',
                'mileage'            => 10000,
                'body_type_id'       => 5,
                'color'              => 'white',
                'transmission'       => 'AT',
                'fuel_type'          => 'gasoline',
                'region_id'          => 11,
                'repair_history'     => 'unknown',
                'created_at'         => $now,
                'updated_at'         => $now,
            ],
        ]);
    }
}