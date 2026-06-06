<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    protected $connection = 'mst';

    public function up(): void
    {
        Schema::connection('mst')->create('mst_vehicle_taxes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('version_id')->nullable()->comment('バージョンID');
            $table->unsignedBigInteger('displacement_list_id')->comment('排気量リストID');
            $table->boolean('is_light')->default(false)->comment('軽自動車フラグ');
            $table->decimal('amount', 10, 0)->comment('自動車税（円）');
            $table->timestamps();

            $table->index('displacement_list_id', 'idx_displacement_list_id');

            $table->foreign('displacement_list_id')
                ->references('id')
                ->on('mst_displacement_lists')
                ->onUpdate('cascade')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::connection('mst')->dropIfExists('mst_vehicle_taxes');
    }
};