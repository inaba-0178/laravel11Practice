<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $connection = 'user';

    public function up(): void
    {
        if (!Schema::connection('user')->hasTable('stk_dealer_reviews')) {
            Schema::connection('user')->create('stk_dealer_reviews', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('dealer_id')->comment('販売店ID');
                $table->unsignedBigInteger('member_id')->comment('会員ID');
                $table->unsignedTinyInteger('rating')->comment('評価（1〜5）');
                $table->text('comment')->nullable()->comment('クチコミ内容');
                $table->timestamps();
                $table->softDeletes();

                $table->foreign('dealer_id')
                    ->references('id')
                    ->on('stk_car_dealers')
                    ->onDelete('cascade');

                $table->index('dealer_id', 'idx_dealer_id');
                $table->index('member_id', 'idx_member_id');
            });
        }
    }

    public function down(): void
    {
        Schema::connection('user')->dropIfExists('stk_dealer_reviews');
    }
};