<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $connection = 'user';

    public function up(): void
    {
        Schema::connection('user')->create('stk_car_stats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('car_id')
                ->constrained('stk_cars')
                ->cascadeOnDelete()
                ->cascadeOnUpdate()
                ->comment('車両ID');
            $table->integer('view_count')->default(0)->comment('閲覧数');
            $table->integer('favorite_count')->default(0)->comment('お気に入り数');
            $table->integer('inquiry_count')->default(0)->comment('問い合わせ数');
            $table->timestamp('last_viewed_at')->nullable()->comment('最終閲覧日時');
            $table->timestamps();

            $table->unique('car_id', 'unique_car');
            $table->index('view_count', 'idx_view_count');
            $table->index('favorite_count', 'idx_favorite_count');
            $table->index('last_viewed_at', 'idx_last_viewed');
        });
    }

    public function down(): void
    {
        Schema::connection('user')->dropIfExists('stk_car_stats');
    }
};