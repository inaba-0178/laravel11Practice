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
        Schema::connection('mst')->create('mst_car_series_body_types', function (Blueprint $table) {
            $table->id();
            $table->foreignId('series_id')
                ->constrained('mst_car_series', 'series_id')
                ->cascadeOnDelete()
                ->cascadeOnUpdate()
                ->comment('車種ID');
            $table->foreignId('body_type_id')
                ->constrained('mst_body_types')
                ->cascadeOnDelete()
                ->cascadeOnUpdate()
                ->comment('ボディタイプID');
            $table->tinyInteger('is_primary')->nullable()->default('0')->comment('代表的なボディタイプフラグ');
            $table->integer('sort_order')->nullable()->default('1000')->comment('表示順');
            $table->timestamps();

            $table->unique(['series_id', 'body_type_id'], 'unique_series_body');
            $table->index('series_id', 'mst_car_series_body_types_series_id_index');
            $table->index('body_type_id', 'mst_car_series_body_types_body_type_id_index');
            $table->index('is_primary', 'mst_car_series_body_types_is_primary_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('mst')->dropIfExists('mst_car_series_body_types');
    }
};