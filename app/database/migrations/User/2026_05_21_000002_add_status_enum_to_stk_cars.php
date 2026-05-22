<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    protected $connection = 'user';

    public function up(): void
    {
        DB::connection('user')->statement("
            ALTER TABLE `stk_cars`
            MODIFY COLUMN `status`
            ENUM('draft','pending','approved_pending','available','reserved','rejected','deleted')
            NOT NULL DEFAULT 'draft'
            COMMENT '在庫管理'
        ");
    }

    public function down(): void
    {
        DB::connection('user')->statement("
            ALTER TABLE `stk_cars`
            MODIFY COLUMN `status`
            ENUM('draft','pending','available','reserved','rejected','deleted')
            NOT NULL DEFAULT 'draft'
            COMMENT '在庫管理'
        ");
    }
};