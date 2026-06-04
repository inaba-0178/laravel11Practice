<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $connection = 'user';

    public function up(): void
    {
        Schema::connection('user')->create('stk_analytics_views', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('dealer_id')->comment('ディーラーID');
            $table->unsignedBigInteger('car_id')->nullable()->comment('車両ID（NULLはディーラーページ）');
            $table->string('member_id', 36)->nullable()->comment('会員ID（未ログイン時はNULL）');
            $table->string('cookie_id', 255)->nullable()->comment('未ログイン時のCookie識別子');
            $table->timestamp('viewed_at')->comment('閲覧日時');
            $table->timestamps();

            $table->index('dealer_id', 'idx_dealer_id');
            $table->index('car_id', 'idx_car_id');
            $table->index('member_id', 'idx_member_id');
            $table->index('cookie_id', 'idx_cookie_id');
            $table->index('viewed_at', 'idx_viewed_at');

            $table->foreign('dealer_id')
                ->references('id')
                ->on('stk_car_dealers')
                ->onDelete('cascade');

            $table->foreign('car_id')
                ->references('id')
                ->on('stk_cars')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::connection('user')->dropIfExists('stk_analytics_views');
    }
};