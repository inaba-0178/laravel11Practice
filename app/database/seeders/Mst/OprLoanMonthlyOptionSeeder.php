<?php

namespace Database\Seeders\Mst;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OprLoanMonthlyOptionSeeder extends Seeder
{
    public function run(): void
    {
        DB::connection('mst')->table('opr_loan_monthly_options')->truncate();

        $records = [
            ['value' => 10000,  'label' => '1万円',  'sort_order' => 1],
            ['value' => 20000,  'label' => '2万円',  'sort_order' => 2],
            ['value' => 30000,  'label' => '3万円',  'sort_order' => 3],
            ['value' => 50000,  'label' => '5万円',  'sort_order' => 4],
            ['value' => 100000, 'label' => '10万円', 'sort_order' => 5],
        ];

        foreach ($records as &$record) {
            $record['is_active']  = true;
            $record['created_at'] = now();
            $record['updated_at'] = now();
        }

        DB::connection('mst')->table('opr_loan_monthly_options')->insert($records);
    }
}