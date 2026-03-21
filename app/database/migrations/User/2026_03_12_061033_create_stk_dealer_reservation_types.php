<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $connection = 'user';

    public function up(): void
    {
        if (!Schema::connection('user')->hasTable('stk_dealer_reservation_types')) {
            Schema::connection('user')->create('stk_dealer_reservation_types', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('dealer_id')->comment('販売店ID');
                $table->unsignedBigInteger('reservation_type_id')->comment('予約種別ID（opr_reservation_types）');
                $table->tinyInteger('is_active')->default(1)->comment('許可フラグ');
                $table->timestamps();

                $table->foreign('dealer_id')
                    ->references('id')
                    ->on('stk_car_dealers')
                    ->onDelete('cascade');

                $table->unique(['dealer_id', 'reservation_type_id'], 'unique_dealer_reservation_type');
                $table->index('dealer_id', 'idx_dealer_id');
                $table->index('reservation_type_id', 'idx_reservation_type_id');
            });
        }
    }

    public function down(): void
    {
        Schema::connection('user')->dropIfExists('stk_dealer_reservation_types');
    }
};