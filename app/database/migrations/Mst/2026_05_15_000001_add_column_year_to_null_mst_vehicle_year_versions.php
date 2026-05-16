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
            $table->year('year_to')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::connection('mst')->table('mst_vehicle_year_versions', function (Blueprint $table) {
            $table->year('year_to')->nullable(false)->change();
        });
    }
};