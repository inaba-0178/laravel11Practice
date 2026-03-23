<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::connection('user')->table('usr_users', function (Blueprint $table) {
            $table->enum('status', ['active', 'caution', 'blacklist'])
                  ->default('active')
                  ->comment('ユーザーステータス')
                  ->after('id');
        });
    }

    public function down(): void
    {
        Schema::connection('user')->table('usr_users', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};