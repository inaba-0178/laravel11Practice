<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::connection('mst_backup')->table('mst_car_series_images_backups', function (Blueprint $table) {
            // AUTO_INCREMENTを外してからPRIMARY KEY削除
            $table->unsignedBigInteger('id')->change();
            $table->dropPrimary();
            $table->primary(['version_id', 'id']);
        });
    }

    public function down(): void
    {
        Schema::connection('mst_backup')->table('mst_car_series_images_backups', function (Blueprint $table) {
            $table->dropPrimary();
            $table->primary('id');
        });
    }
};