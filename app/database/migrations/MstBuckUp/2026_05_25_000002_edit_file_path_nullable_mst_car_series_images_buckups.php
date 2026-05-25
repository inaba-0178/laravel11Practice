<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::connection('mst_backup')->table('mst_car_series_images_backups', function (Blueprint $table) {
            $table->string('file_path', 255)->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::connection('mst_backup')->table('mst_car_series_images_backups', function (Blueprint $table) {
            $table->string('file_path', 255)->nullable(false)->change();
        });
    }
};