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
        Schema::connection('mst')->create('opr_car_options', function (Blueprint $table) {
            $table->id();
            $table->foreignId('car_id')
                ->constrained('opr_cars')
                ->cascadeOnDelete()
                ->cascadeOnUpdate()
                ->comment('車両ID');
            $table->enum('option_category', ['basic', 'safety', 'environmental', 'audio', 'navigation', 'seat', 'dress_up', 'other'])->comment('オプション種別');
            $table->string('option_name', 255)->comment('画像種別');
            $table->boolean('is_equipped')->default(1)->comment('装備有無');
            $table->integer('display_order')->default(1000)->comment('表示順');
            $table->timestamps();

            $table->index('car_id', 'idx_car_id');
            $table->index('option_category', 'idx_option_category');
            $table->index('is_equipped', 'idx_is_equipped');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('mst')->dropIfExists('opr_car_options');
    }
};