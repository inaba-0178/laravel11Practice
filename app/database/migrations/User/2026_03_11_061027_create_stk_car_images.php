<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $connection = 'user';

    public function up(): void
    {
        Schema::connection('user')->create('stk_car_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('car_id')
                ->constrained('stk_cars')
                ->cascadeOnDelete()
                ->cascadeOnUpdate()
                ->comment('車両ID');
            $table->string('image_url', 500)->nullable()->comment('画像URL');
            $table->enum('image_type', ['exterior', 'interior', 'engine', 'other'])->default('exterior')->comment('画像種別');
            $table->integer('display_order')->default(1000)->comment('表示順');
            $table->boolean('is_main')->default(0)->comment('メイン画像フラグ');
            $table->timestamps();

            $table->index('car_id', 'idx_car_id');
            $table->index('display_order', 'idx_display_order');
            $table->index('is_main', 'idx_is_main');
        });
    }

    public function down(): void
    {
        Schema::connection('user')->dropIfExists('stk_car_images');
    }
};