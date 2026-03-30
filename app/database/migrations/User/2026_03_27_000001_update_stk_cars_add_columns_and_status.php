<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $connection = 'user';

    public function up(): void
    {
        // ===== statusにdraft追加 =====
        DB::connection($this->connection)->statement("
            ALTER TABLE `stk_cars`
            MODIFY COLUMN `status`
                ENUM('draft','pending','available','reserved','rejected','deleted')
                NOT NULL
                DEFAULT 'draft'
                COMMENT '在庫管理'
                COLLATE 'utf8mb4_unicode_ci'
        ");

        // ===== manufacturer_id・year_version_id追加 =====
        Schema::connection($this->connection)->table('stk_cars', function (Blueprint $table) {
            $table->unsignedBigInteger('manufacturer_id')
                ->nullable()
                ->comment('メーカーID')
                ->after('dealer_id');

            $table->unsignedBigInteger('year_version_id')
                ->nullable()
                ->comment('年式バージョンID')
                ->after('vehicle_id');
        });
    }

    public function down(): void
    {
        Schema::connection($this->connection)->table('stk_cars', function (Blueprint $table) {
            $table->dropColumn(['manufacturer_id', 'year_version_id']);
        });

        DB::connection($this->connection)->statement("
            UPDATE `stk_cars`
            SET `status` = 'pending'
            WHERE `status` = 'draft'
        ");

        DB::connection($this->connection)->statement("
            ALTER TABLE `stk_cars`
            MODIFY COLUMN `status`
                ENUM('pending','available','reserved','sold','rejected','deleted')
                NOT NULL
                DEFAULT 'pending'
                COMMENT '在庫管理'
                COLLATE 'utf8mb4_unicode_ci'
        ");
    }
};