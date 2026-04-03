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
        Schema::connection('mst')->table('mst_vehicle_year_versions', function (Blueprint $table) {
            // 1. 外部キー制約を先に削除
            $table->dropForeign(['vehicle_id']);
            
            // 2. ユニークインデックスを削除
            $table->dropUnique('unique_vehicle_year');
            
            // 3. year_model を削除
            $table->dropColumn('year_model');
            
            // 4. year_from / year_to を追加
            $table->year('year_from')->after('vehicle_id');
            $table->year('year_to')->after('year_from');
            
            // 5. 新しいユニークインデックスを追加
            $table->unique(['vehicle_id', 'year_from', 'year_to'], 'unique_vehicle_year_range');
            
            // 6. 外部キー制約を再追加
            $table->foreign('vehicle_id')->references('id')->on('mst_vehicles');
        });
    }

    public function down(): void
    {
        Schema::connection($this->connection)->table('mst_vehicle_year_versions', function (Blueprint $table) {
            $table->year('year_model')->nullable()->after('vehicle_id');
        });

        DB::connection($this->connection)->statement('
            UPDATE mst_vehicle_year_versions SET year_model = year_from
        ');

        Schema::connection($this->connection)->table('mst_vehicle_year_versions', function (Blueprint $table) {
            $table->dropColumn('year_from');
            $table->dropColumn('year_to');
        });
    }
};