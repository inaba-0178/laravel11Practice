<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $connection = 'mst';

    public function up(): void
    {
        Schema::connection('mst')->create('mst_color_options', function (Blueprint $table) {
            $table->id();
            $table->string('value', 50)->comment('値（APIキー）');
            $table->string('label', 100)->comment('表示名');
            $table->string('hex_code', 7)->nullable()->comment('カラーコード（例：#ffffff）');
            $table->unsignedTinyInteger('sort_order')->default(0)->comment('表示順');
            $table->boolean('is_active')->default(true)->comment('表示フラグ');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::connection('mst')->dropIfExists('mst_color_options');
    }
};