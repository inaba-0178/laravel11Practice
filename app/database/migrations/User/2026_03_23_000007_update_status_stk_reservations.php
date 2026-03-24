<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 既存のcancelledデータをcancelled_customerに変換
        DB::connection('user')->table('stk_reservations')
            ->where('status', 'cancelled')
            ->update(['status' => 'cancelled_customer']);

        // ENUMを更新
        DB::connection('user')->statement("
            ALTER TABLE stk_reservations 
            MODIFY COLUMN status ENUM(
                'pending',
                'confirmed',
                'completed',
                'no_show',
                'denial',
                'cancelled_dealer_car_sold',
                'cancelled_dealer_trouble',
                'cancelled_customer',
                'cancelled_by_system',
            ) NOT NULL DEFAULT 'pending'
        ");
    }

    public function down(): void
    {
        DB::connection('user')->table('stk_reservations')
            ->whereIn('status', ['cancelled_dealer_car_sold', 'cancelled_dealer_trouble', 'cancelled_customer'])
            ->update(['status' => 'cancelled']);

        DB::connection('user')->statement("
            ALTER TABLE stk_reservations 
            MODIFY COLUMN status ENUM(
                'pending',
                'confirmed',
                'completed',
                'no_show',
                'denial',
                'cancelled'
            ) NOT NULL DEFAULT 'pending'
        ");
    }
};