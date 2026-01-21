<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $connection = 'mst';
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('mst_regions', function (Blueprint $table) {
            $table->id();
            $table->integer('area_code')->nullable();
            $table->string('name');
            $table->string('url')->nullable();
            $table->string('query_param')->nullable();
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('mst')->dropIfExists('mst_regions');
    }
};
