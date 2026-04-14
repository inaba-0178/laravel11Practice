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
            $table->dropColumn('business_hours');
            $table->string('business_hours_from', 5)->nullable()->comment('営業開始時間（例：10:00）')->after('regular_holiday');
            $table->string('business_hours_to', 5)->nullable()->comment('営業終了時間（例：19:00）')->after('business_hours_from');
        });
    }

    public function down(): void
    {
        Schema::connection('user')->table('stk_car_dealers', function (Blueprint $table) {
            $table->dropColumn(['business_hours_from', 'business_hours_to']);
            $table->string('business_hours')->nullable()->comment('営業時間')->after('regular_holiday');
        });
    }
};