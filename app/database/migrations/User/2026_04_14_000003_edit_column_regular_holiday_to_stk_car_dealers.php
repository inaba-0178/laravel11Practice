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
            $table->dropColumn('regular_holiday');
            $table->string('regular_holiday_days', 255)->nullable()->comment('定休日曜日（例：火曜,水曜）')->after('business_hours_to');
            $table->boolean('regular_holiday_except_holiday')->default(false)->comment('祝日除くフラグ')->after('regular_holiday_days');
        });
    }

    public function down(): void
    {
        Schema::connection('user')->table('stk_car_dealers', function (Blueprint $table) {
            $table->dropColumn(['regular_holiday_days', 'regular_holiday_except_holiday']);
            $table->string('regular_holiday', 255)->nullable()->comment('定休日')->after('business_hours_to');
        });
    }
};