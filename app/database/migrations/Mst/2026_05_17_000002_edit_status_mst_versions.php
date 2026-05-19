<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    protected $connection = 'mst';

    public function up(): void
    {
        // 先に既存データをすべてarchivedに変換
        DB::connection('mst')->statement("
            UPDATE mst_versions SET status = 'archived'
        ");

        // 最新のものをactiveに変更
        DB::connection('mst')->statement("
            UPDATE mst_versions SET status = 'active' 
            WHERE id = (SELECT id FROM (SELECT MAX(id) as id FROM mst_versions) as t)
        ");

        // ENUMを変更
        DB::connection('mst')->statement("
            ALTER TABLE mst_versions 
            MODIFY COLUMN `status` ENUM('active', 'archived') NOT NULL DEFAULT 'archived'
        ");
    }

    public function down(): void
    {
        DB::connection('mst')->statement("
            ALTER TABLE mst_versions 
            MODIFY COLUMN `status` ENUM('draft','pending','approved','active','archived') NOT NULL DEFAULT 'draft'
        ");
    }
};