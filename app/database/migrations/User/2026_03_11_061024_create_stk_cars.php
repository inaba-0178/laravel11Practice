<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $connection = 'user';

    public function up(): void
    {
        Schema::connection('user')->create('stk_cars', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dealer_id')
                ->constrained('stk_car_dealers')
                ->cascadeOnDelete()
                ->cascadeOnUpdate()
                ->comment('販売店ID');
            $table->unsignedBigInteger('series_id')->comment('車種シリーズID');
            $table->unsignedBigInteger('vehicle_id')->comment('車両ID');
            $table->string('stock_number', 50)->nullable()->comment('在庫番号');
            $table->enum('status', ['available', 'reserved', 'sold', 'deleted'])->default('available')->comment('在庫管理');
            $table->decimal('price', 12, 0)->comment('価格');
            $table->enum('price_display_type', ['actual', 'negotiable', 'ask'])->default('actual')->comment('価格表示方法');
            $table->year('model_year')->nullable()->comment('年式');
            $table->integer('mileage')->comment('走行距離(km)');
            $table->unsignedBigInteger('body_type_id')->nullable()->comment('ボディタイプID');
            $table->string('color', 100)->comment('ボディカラー');
            $table->enum('transmission', ['AT', 'MT', 'CVT', 'DCT', 'other'])->nullable()->comment('トランスミッション');
            $table->enum('fuel_type', ['gasoline', 'diesel', 'hybrid', 'electric', 'phev', 'other'])->nullable()->comment('燃料タイプ');
            $table->unsignedBigInteger('region_id')->comment('都道府県ID');
            $table->enum('repair_history', ['none', 'minor', 'major', 'unknown'])->default('unknown')->comment('修復歴');
            $table->string('main_image_url', 500)->nullable()->comment('メイン画像URL');
            $table->timestamp('published_at')->nullable()->comment('公開日時');
            $table->timestamp('sold_at')->nullable()->comment('売却日時');
            $table->timestamps();
            $table->softDeletes();

            $table->index('dealer_id', 'idx_dealer');
            $table->index('series_id', 'idx_series_id');
            $table->index('vehicle_id', 'idx_vehicle');
            $table->index('status', 'idx_status');
            $table->index('price', 'idx_price');
            $table->index('model_year', 'idx_model_year');
            $table->index('mileage', 'idx_mileage');
            $table->index('body_type_id', 'idx_body_type');
            $table->index('transmission', 'idx_transmission');
            $table->index('fuel_type', 'idx_fuel_type');
            $table->index('repair_history', 'idx_repair_history');
            $table->index('published_at', 'idx_published_at');
            $table->index(['status', 'published_at'], 'idx_status_published');
        });
    }

    public function down(): void
    {
        Schema::connection('user')->dropIfExists('stk_cars');
    }
};