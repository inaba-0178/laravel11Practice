<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    protected $connection = 'user';

    public function up(): void
    {
        DB::connection('user')->statement("
            ALTER TABLE users 
            MODIFY COLUMN role 
            ENUM('super','admin','staff','tester','dealer','dealer_staff') 
            NOT NULL DEFAULT 'dealer'
            COMMENT 'ロール'
        ");
    }

    public function down(): void
    {
        DB::connection('user')->statement("
            ALTER TABLE users 
            MODIFY COLUMN role 
            ENUM('super','admin','dealer','dealer_staff') 
            NOT NULL DEFAULT 'dealer'
            COMMENT 'ロール'
        ");
    }
};