<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $connection = 'user';

    public function up(): void
    {
        Schema::connection('user')->table('stk_car_dealers', function (Blueprint $table) {
            $table->tinyInteger('is_maker_dealer')
                ->default(0)
                ->comment('メーカー系販売店フラグ')
                ->after('dealer_type');
        });
    }

    public function down(): void
    {
        Schema::connection('user')->table('stk_car_dealers', function (Blueprint $table) {
            $table->dropColumn('is_maker_dealer');
        });
    }
};