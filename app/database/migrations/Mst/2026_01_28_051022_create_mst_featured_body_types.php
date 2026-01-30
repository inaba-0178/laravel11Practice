<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::connection('mst')->create('mst_featured_body_types', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('body_type_code', 50);
            $table->string('position', 20);  // 'top-hi', 'top-row'
            $table->unsignedInteger('sort_order')->default(1000);
            $table->tinyInteger('is_active')->default(1);
            $table->timestamps();
            $table->softDeletes();
            
            $table->unique(['body_type_code', 'position']);
            $table->index(['position', 'sort_order', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::connection('mst')->dropIfExists('mst_body_type_brands');
    }
};
