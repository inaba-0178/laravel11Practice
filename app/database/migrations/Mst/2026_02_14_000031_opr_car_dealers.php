<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $connection = 'mst';

    public function up(): void
{
    Schema::connection('mst')->table('opr_car_dealers', function (Blueprint $table) {
        // 1. 既存のregion_id列を先に削除（存在する場合）
        if (Schema::connection('mst')->hasColumn('opr_car_dealers', 'region_id')) {
            DB::connection('mst')->statement('ALTER TABLE `opr_car_dealers` DROP FOREIGN KEY IF EXISTS `opr_car_dealers_region_id_foreign`');
            $table->dropColumn('region_id');
        }

        // 3. postal_code直後にregion_idを正確に配置＋外部キー
        $table->foreignId('region_id')
            ->after('postal_code')
            ->constrained('mst_regions', 'id')
            ->restrictOnDelete()
            ->cascadeOnUpdate()
            ->comment('都道府県ID');

        
        $table->index('region_id', 'idx_region');
    });
}

public function down(): void
{
    Schema::connection('mst')->table('opr_car_dealers', function (Blueprint $table) {
        // region_id削除
        DB::connection('mst')->statement('ALTER TABLE `opr_car_dealers` DROP FOREIGN KEY IF EXISTS `opr_car_dealers_region_id_foreign`');
        $table->dropColumn('region_id');
        $table->dropIndex('idx_region');
        
        // area_code復元（postal_codeの後）
        $table->foreignId('area_code')
            ->after('postal_code')
            ->constrained('mst_regions')
            ->restrictOnDelete()
            ->cascadeOnUpdate()
            ->comment('都道府県ID');
        $table->index('area_code', 'idx_prefecture');
    });
}


};