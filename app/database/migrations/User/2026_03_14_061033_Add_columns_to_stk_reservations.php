<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::connection('user')->statement("
            ALTER TABLE stk_reservations 
            MODIFY COLUMN status ENUM('pending','confirmed','cancelled','completed','no_show') 
            NOT NULL DEFAULT 'pending'
        ");

        Schema::connection('user')->table('stk_reservations', function (Blueprint $table) {
            $table->text('visit_reason')->nullable()->comment('来店理由・対応メモ')->after('memo');
            $table->unsignedBigInteger('handled_by')->nullable()->comment('対応者（管理者ID）')->after('visit_reason');
            $table->timestamp('handled_at')->nullable()->comment('対応日時')->after('handled_by');
        });
    }

    public function down(): void
    {
        Schema::connection('user')->table('stk_reservations', function (Blueprint $table) {
            $table->dropColumn(['visit_reason', 'handled_by', 'handled_at']);
        });

        DB::connection('user')->statement("
            ALTER TABLE stk_reservations 
            MODIFY COLUMN status ENUM('pending','confirmed','cancelled') 
            NOT NULL DEFAULT 'pending'
        ");
    }
};