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
            $table->id('series_id');         // series_id (bigint unsigned, auto_increment, primary key)
            $table->string('series_name', 255);
            $table->string('manufacturer_id', 255);
            $table->timestamps();

            $table->unique(['manufacturer_id', 'series_name'], 'unique_series'); 
        });
    }

    public function down(): void
    {
        Schema::connection('mst')->dropIfExists('mst_car_series');
    }
};
