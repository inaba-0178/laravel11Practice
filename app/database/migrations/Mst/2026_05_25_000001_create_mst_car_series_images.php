<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::connection('mst')->create('mst_car_series_images', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('version_id')->nullable()->comment('バージョンID');
            $table->unsignedBigInteger('series_id');
            $table->string('file_path', 255);
            $table->string('alt_text', 255)->nullable();
            $table->unsignedInteger('sort_order')->default(1000);
            $table->tinyInteger('is_main')->default(0);
            $table->tinyInteger('is_active')->default(1);
            $table->timestamps();

            $table->index('series_id');
            $table->foreign('series_id')->references('series_id')->on('mst_car_series');
        });
    }

    public function down(): void
    {
        Schema::connection('mst')->dropIfExists('mst_car_series_images');
    }
};