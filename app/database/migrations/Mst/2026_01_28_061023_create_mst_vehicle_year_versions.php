<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $connection = 'mst';

    public function up(): void
    {
        Schema::connection('mst')->create('mst_vehicle_year_versions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('vehicle_id');
            $table->year('year_model');
            $table->unsignedInteger('displacement_cc')->nullable();
            $table->enum('drive_type', ['2WD', '4WD', 'FR', 'FF'])->default('2WD');
            $table->decimal('fuel_efficiency_from', 5, 1)->nullable();
            $table->decimal('fuel_efficiency_to', 5, 1)->nullable();
            $table->unsignedInteger('max_power_kw')->nullable();
            $table->enum('transmission_type', ['AT', 'MT', 'CVT'])->default('AT');
            $table->unsignedInteger('weight_kg')->nullable();
            $table->decimal('price_range_from', 10, 0)->nullable();
            $table->decimal('price_range_to', 10, 0)->nullable();
            $table->boolean('is_latest')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['vehicle_id', 'year_model'], 'unique_vehicle_year');
            $table->foreign('vehicle_id')->references('id')->on('mst_vehicles');          
        });
    }

    public function down(): void
    {
        Schema::connection('mst')->dropIfExists('mst_vehicle_year_versions');
    }
};
