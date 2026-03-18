<?php

namespace Database\Seeders\Mst;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OprLoanDownOptionSeeder extends Seeder
{
    public function run(): void
    {
        DB::connection('mst')->table('opr_loan_down_options')->truncate();

        $records = [
            ['value' => 0,      'label' => '0円',   'sort_order' => 1],
            ['value' => 100000, 'label' => '10万円', 'sort_order' => 2],
            ['value' => 300000, 'label' => '30万円', 'sort_order' => 3],
            ['value' => 500000, 'label' => '50万円', 'sort_order' => 4],
        ];

        foreach ($records as &$record) {
            $record['is_active']  = true;
            $record['created_at'] = now();
            $record['updated_at'] = now();
        }

        DB::connection('mst')->table('opr_loan_down_options')->insert($records);
    }
}