<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $connection = 'mst';
    
    public function up(): void
    {
        Schema::create('mst_areas', function (Blueprint $table) {
            $table->id();
            $table->string('name', 50)->comment('地方名');
            $table->string('query_param', 50)->comment('地方URl');
            $table->integer('sort_order')->default(0)->comment('表示順');
            $table->timestamps();
            
            $table->index('sort_order');
        });
    }

    public function down(): void
    {
        Schema::connection('mst')->dropIfExists('mst_areas');
    }
};