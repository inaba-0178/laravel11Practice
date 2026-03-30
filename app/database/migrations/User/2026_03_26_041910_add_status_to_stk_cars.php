<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * mst DBではなくuser DBに対して実行
     */
    protected $connection = 'user';

    public function up(): void
    {
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

    public function down(): void
    {
        // pending/rejectedをavailableに変換してからENUM変更
        DB::connection($this->connection)->statement("
            UPDATE `stk_cars`
            SET `status` = 'available'
            WHERE `status` IN ('pending', 'rejected')
        ");

        DB::connection($this->connection)->statement("
            ALTER TABLE `stk_cars`
            MODIFY COLUMN `status`
                ENUM('available','reserved','sold','deleted')
                NOT NULL
                DEFAULT 'available'
                COMMENT '在庫管理'
                COLLATE 'utf8mb4_unicode_ci'
        ");
    }
};