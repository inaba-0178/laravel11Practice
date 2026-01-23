<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $connection = 'mst';
    
    public function up(): void
    {
        Schema::create('mst_mileage_lists', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->decimal('min_amount', 10, 0)->nullable();
            $table->decimal('max_amount', 10, 0)->nullable();
            $table->boolean('is_unlimited')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::connection('mst')->dropIfExists('mst_mileage_lists');
    }
};