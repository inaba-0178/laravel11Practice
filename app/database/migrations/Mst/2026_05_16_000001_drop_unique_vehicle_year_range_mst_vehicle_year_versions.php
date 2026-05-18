<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $connection = 'mst';

    public function up(): void
    {
        Schema::connection('mst')->table('mst_vehicle_year_versions', function (Blueprint $table) {
            $table->dropForeign('mst_vehicle_year_versions_vehicle_id_foreign');
            $table->dropUnique('unique_vehicle_year_range');
            $table->foreign('vehicle_id')->references('id')->on('mst_vehicles');
        });
    }

    public function down(): void
    {
        Schema::connection('mst')->table('mst_vehicle_year_versions', function (Blueprint $table) {
            $table->dropForeign('mst_vehicle_year_versions_vehicle_id_foreign');
            $table->unique(['vehicle_id', 'year_from', 'year_to'], 'unique_vehicle_year_range');
            $table->foreign('vehicle_id')->references('id')->on('mst_vehicles');
        });
    }
};