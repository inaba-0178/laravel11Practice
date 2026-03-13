<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $connection = 'user';

    public function up(): void
    {
        Schema::connection('user')->create('stk_car_dealers', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255)->comment('販売店名');
            $table->string('postal_code', 8)->nullable()->comment('郵便番号');
            $table->unsignedBigInteger('region_id')->comment('都道府県ID');
            $table->unsignedBigInteger('area_code')->nullable()->comment('エリアコード（関東・東北など）');
            $table->string('city', 100)->comment('市区町村');
            $table->string('address_detail', 255)->nullable()->comment('番地・建物');
            $table->string('phone', 20)->nullable()->comment('電話番号');
            $table->string('email', 255)->nullable()->comment('メールアドレス');
            $table->string('website_url', 500)->nullable()->comment('ホームページURL');
            $table->text('business_hours')->nullable()->comment('営業時間');
            $table->string('regular_holiday', 255)->nullable()->comment('定休日');
            $table->enum('dealer_type', ['new_car', 'used_car', 'both'])->default('used_car')->comment('ディーラー種別');
            $table->text('free_text')->nullable()->comment('フリーワード');
            $table->decimal('latitude', 10, 7)->nullable()->comment('緯度');
            $table->decimal('longitude', 10, 7)->nullable()->comment('経度');
            $table->boolean('is_active')->default(1)->comment('公開フラグ');
            $table->timestamps();
            $table->softDeletes();

            $table->index('region_id', 'idx_region');
            $table->index('dealer_type', 'idx_dealer_type');
            $table->index('is_active', 'idx_is_active');
            $table->index('area_code', 'idx_area_code');
            $table->index(['latitude', 'longitude'], 'idx_location');
        });
    }

    public function down(): void
    {
        Schema::connection('user')->dropIfExists('stk_car_dealers');
    }
};