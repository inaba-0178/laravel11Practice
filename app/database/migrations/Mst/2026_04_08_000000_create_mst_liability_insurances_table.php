<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $connection = 'mst';

    public function up(): void
    {
        Schema::connection('mst')->create('mst_liability_insurances', function (Blueprint $table) {
            $table->id();
            $table->enum('vehicle_type', ['light', 'standard'])->comment('車種区分');
            $table->unsignedInteger('months')->comment('保険期間（ヶ月）');
            $table->decimal('amount', 10, 0)->comment('自賠責保険料（円）');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::connection('mst')->dropIfExists('mst_liability_insurances');
    }
};