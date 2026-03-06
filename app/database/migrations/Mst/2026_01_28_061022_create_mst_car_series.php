<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $connection = 'mst';

    public function up(): void
    {
        Schema::create('mst_car_series', function (Blueprint $table) {
            $table->unsignedBigInteger('series_id')->primary();
            $table->string('series_name', 255);
            $table->unsignedBigInteger('manufacturer_id');
            $table->timestamps();

            $table->unique(['manufacturer_id', 'series_name'], 'unique_series'); 
        });
    }

    public function down(): void
    {
        Schema::connection('mst')->dropIfExists('mst_car_series');
    }
};
