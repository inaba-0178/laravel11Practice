<?php
// database/migrations/Mst/xxxx_create_mst_featured_brands_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::connection('mst')->create('mst_featured_brands', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('manufacturer_code', 50);
            $table->string('position', 20);  // 'jp-top-row', 'import-top-row'
            $table->unsignedInteger('sort_order')->default(1000);
            $table->tinyInteger('is_active')->default(1);
            $table->timestamps();
            $table->softDeletes();
            
            $table->unique(['manufacturer_code', 'position']);
            $table->index(['position', 'sort_order', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::connection('mst')->dropIfExists('mst_featured_brands');
    }
};
