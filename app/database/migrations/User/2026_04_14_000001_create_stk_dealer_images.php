<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $connection = 'user';

    public function up(): void
    {
        Schema::connection('user')->create('stk_dealer_images', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('dealer_id');
            $table->string('image_path')->comment('MinIO画像パス');
            $table->string('alt_text')->nullable()->comment('代替テキスト');
            $table->boolean('is_main')->default(false)->comment('メイン画像フラグ');
            $table->unsignedTinyInteger('sort_order')->default(0)->comment('表示順');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('dealer_id')->references('id')->on('stk_car_dealers');
        });
    }

    public function down(): void
    {
        Schema::connection('user')->dropIfExists('stk_dealer_images');
    }
};