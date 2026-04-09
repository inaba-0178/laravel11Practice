<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $connection = 'mst';

    public function up(): void
    {
        Schema::connection('mst')->create('mst_vehicle_weight_taxes', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('weight_from')->comment('重量（kg・以上）');
            $table->unsignedInteger('weight_to')->comment('重量（kg・未満）');
            $table->boolean('is_light')->default(false)->comment('軽自動車フラグ');
            $table->decimal('amount', 10, 0)->comment('重量税（円）');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::connection('mst')->dropIfExists('mst_vehicle_weight_taxes');
    }
};