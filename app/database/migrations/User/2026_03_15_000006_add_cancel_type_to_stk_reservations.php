<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::connection('user')->table('stk_reservations', function (Blueprint $table) {
            $table->enum('cancel_type', ['dealer_car_sold', 'dealer_trouble', 'customer'])
                  ->nullable()
                  ->comment('キャンセル区分')
                  ->after('status');
        });
    }

    public function down(): void
    {
        Schema::connection('user')->table('stk_reservations', function (Blueprint $table) {
            $table->dropColumn('cancel_type');
        });
    }
};