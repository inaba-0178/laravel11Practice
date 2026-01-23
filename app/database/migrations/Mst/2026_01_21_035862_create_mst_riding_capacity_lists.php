<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $connection = 'mst';
    
    public function up(): void
    {
        Schema::create('mst_riding_capacity_lists', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->decimal('max_amount', 2, 0);
            $table->boolean('is_unlimited')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::connection('mst')->dropIfExists('mst_riding_capacity_lists');
    }
};