<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $connection = 'user';

    public function up(): void
    {
        if (!Schema::connection('user')->hasTable('stk_dealer_schedules')) {
            Schema::connection('user')->create('stk_dealer_schedules', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('dealer_id')->comment('販売店ID');
                $table->unsignedBigInteger('reservation_type_id')->comment('予約種別ID');
                $table->date('date')->comment('予約可能日');
                $table->time('time_from')->comment('開始時間');
                $table->time('time_to')->comment('終了時間');
                $table->unsignedInteger('max_reservations')->default(1)->comment('最大予約数');
                $table->tinyInteger('is_available')->default(1)->comment('予約可否フラグ');
                $table->timestamps();

                $table->foreign('dealer_id')
                    ->references('id')
                    ->on('stk_car_dealers')
                    ->onDelete('cascade');

                $table->index(['dealer_id', 'date'], 'idx_dealer_date');
                $table->index('reservation_type_id', 'idx_reservation_type_id');
            });
        }
    }

    public function down(): void
    {
        Schema::connection('user')->dropIfExists('stk_dealer_schedules');
    }
};