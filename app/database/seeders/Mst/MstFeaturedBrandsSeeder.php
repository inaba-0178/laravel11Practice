<?php

namespace Database\Seeders\Mst;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MstFeaturedBrandsSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        DB::connection('mst')->table('mst_featured_brands')->insert([
            // 国産車
            ['manufacturer_code' => 'LEXUS01',     'position' => 'jp-top-row',     'sort_order' => 100, 'is_active' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['manufacturer_code' => 'TOYOTA01',    'position' => 'jp-top-row',     'sort_order' => 110, 'is_active' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['manufacturer_code' => 'HONDA01',     'position' => 'jp-top-row',     'sort_order' => 120, 'is_active' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['manufacturer_code' => 'NISSAN01',    'position' => 'jp-top-row',     'sort_order' => 130, 'is_active' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['manufacturer_code' => 'SUZUKI01',    'position' => 'jp-top-row',     'sort_order' => 140, 'is_active' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['manufacturer_code' => 'DAIHATSU01',  'position' => 'jp-top-row',     'sort_order' => 150, 'is_active' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['manufacturer_code' => 'MAZDA01',     'position' => 'jp-top-row',     'sort_order' => 160, 'is_active' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['manufacturer_code' => 'SUBARU01',    'position' => 'jp-top-row',     'sort_order' => 170, 'is_active' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['manufacturer_code' => 'MITSUBISHI01','position' => 'jp-top-row',     'sort_order' => 180, 'is_active' => 1, 'created_at' => $now, 'updated_at' => $now],
            // 輸入車
            ['manufacturer_code' => 'MBENZ01',     'position' => 'import-top-row', 'sort_order' => 200, 'is_active' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['manufacturer_code' => 'BMW01',       'position' => 'import-top-row', 'sort_order' => 210, 'is_active' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['manufacturer_code' => 'VW01',        'position' => 'import-top-row', 'sort_order' => 220, 'is_active' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['manufacturer_code' => 'AUDI01',      'position' => 'import-top-row', 'sort_order' => 230, 'is_active' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['manufacturer_code' => 'MINI01',      'position' => 'import-top-row', 'sort_order' => 240, 'is_active' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['manufacturer_code' => 'PORSCHE01',   'position' => 'import-top-row', 'sort_order' => 250, 'is_active' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['manufacturer_code' => 'VOLVO01',     'position' => 'import-top-row', 'sort_order' => 260, 'is_active' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['manufacturer_code' => 'PEUGEOT01',   'position' => 'import-top-row', 'sort_order' => 270, 'is_active' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['manufacturer_code' => 'LANDROVER01', 'position' => 'import-top-row', 'sort_order' => 280, 'is_active' => 1, 'created_at' => $now, 'updated_at' => $now],
        ]);
    }
}