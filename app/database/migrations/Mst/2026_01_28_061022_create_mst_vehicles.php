<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $connection = 'mst';

    public function up(): void
    {
        Schema::connection('mst')->create('mst_vehicles', function (Blueprint $table) {
            $table->id(); 
            $table->foreignId('series_id')
              ->constrained('mst_car_series', 'series_id')
              ->cascadeOnDelete();
            $table->unsignedBigInteger('manufacturer_id');
            $table->string('name', 255);
            $table->string('model_code', 50)->nullable();
            $table->string('body_type', 50)->nullable();
            $table->char('country_code', 2)->nullable();
            $table->enum('status', ['active', 'discontinued', 'concept'])
                ->default('active');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::connection('mst')->dropIfExists('mst_vehicles');
    }
};
