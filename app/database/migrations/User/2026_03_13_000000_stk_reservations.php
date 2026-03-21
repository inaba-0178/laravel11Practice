<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $connection = 'user';

    public function up(): void
    {
        if (!Schema::connection('user')->hasTable('stk_reservations')) {
            Schema::connection('user')->create('stk_reservations', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('dealer_id')->comment('販売店ID');
                $table->unsignedBigInteger('car_id')->comment('車両ID');
                $table->string('member_id', 36)->nullable()->comment('会員ID（NULLはゲスト）');
                $table->unsignedBigInteger('reservation_type_id')->comment('予約種別ID');
                $table->unsignedBigInteger('schedule_id')->comment('スケジュールID');
                $table->enum('status', ['pending', 'confirmed', 'cancelled'])->default('pending')->comment('予約ステータス');
                $table->text('memo')->nullable()->comment('備考');

                // ゲスト情報（会員の場合はNULL）
                $table->string('guest_name', 100)->nullable()->comment('ゲスト名');
                $table->string('guest_phone', 20)->nullable()->comment('ゲスト電話番号（メールかどちらか必須）');
                $table->string('guest_email', 255)->nullable()->comment('ゲストメールアドレス（電話かどちらか必須）');
                $table->string('guest_address', 255)->nullable()->comment('ゲスト住所（任意・将来必須化可能）');

                $table->timestamps();
                $table->softDeletes();

                $table->foreign('dealer_id')
                    ->references('id')
                    ->on('stk_car_dealers')
                    ->onDelete('cascade');

                $table->foreign('car_id')
                    ->references('id')
                    ->on('stk_cars')
                    ->onDelete('cascade');

                $table->foreign('schedule_id')
                    ->references('id')
                    ->on('stk_dealer_schedules')
                    ->onDelete('cascade');

                $table->index('dealer_id', 'idx_dealer_id');
                $table->index('car_id', 'idx_car_id');
                $table->index('member_id', 'idx_member_id');
                $table->index('schedule_id', 'idx_schedule_id');
                $table->index('status', 'idx_status');
            });
        }
    }

    public function down(): void
    {
        Schema::connection('user')->dropIfExists('stk_reservations');
    }
};