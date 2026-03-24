<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::connection('user')->statement("
            ALTER TABLE stk_reservations 
            MODIFY COLUMN status ENUM('pending','confirmed','cancelled','completed','no_show','denial') 
            NOT NULL DEFAULT 'pending'
        ");
    }

    public function down(): void
    {
        DB::connection('user')->statement("
            ALTER TABLE stk_reservations 
            MODIFY COLUMN status ENUM('pending','confirmed','cancelled','completed','no_show') 
            NOT NULL DEFAULT 'pending'
        ");
    }
};