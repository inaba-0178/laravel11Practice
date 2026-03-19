<?php

namespace Database\Seeders\mock;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StkDealerReviewSeeder extends Seeder
{
    public function run(): void
    {
        // stk_dealer_reviews
        DB::connection('user')->table('stk_dealer_reviews')->truncate();

        $reviews = [
            ['dealer_id' => 1, 'member_id' => 1, 'rating' => 5, 'comment' => '対応が丁寧で満足しました。'],
            ['dealer_id' => 1, 'member_id' => 2, 'rating' => 4, 'comment' => '車の状態も良く安心して購入できました。'],
            ['dealer_id' => 1, 'member_id' => 3, 'rating' => 5, 'comment' => 'スタッフの説明が分かりやすかった。'],
            ['dealer_id' => 1, 'member_id' => 4, 'rating' => 3, 'comment' => '普通でした。'],
            ['dealer_id' => 1, 'member_id' => 5, 'rating' => 4, 'comment' => 'また利用したいと思います。'],
        ];

        foreach ($reviews as &$review) {
            $review['created_at'] = now();
            $review['updated_at'] = now();
        }

        DB::connection('user')->table('stk_dealer_reviews')->insert($reviews);

        // キャッシュカラムを更新
        $result = DB::connection('user')
            ->table('stk_dealer_reviews')
            ->selectRaw('dealer_id, AVG(rating) as avg_rating, COUNT(*) as total_count')
            ->whereNull('deleted_at')
            ->groupBy('dealer_id')
            ->get();

        foreach ($result as $row) {
            DB::connection('user')
                ->table('stk_car_dealers')
                ->where('id', $row->dealer_id)
                ->update([
                    'review_rating' => round($row->avg_rating, 2),
                    'review_count'  => $row->total_count,
                ]);
        }
    }
}