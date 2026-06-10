<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $connection = 'user';

    public function up(): void
    {
        Schema::connection('user')->table('room_users', function (Blueprint $table) {
            $table->enum('status', ['pending', 'approved', 'rejected'])
                  ->default('approved') // 既存データはapprovedに
                  ->after('user_type');
            $table->timestamp('invited_at')->nullable()->after('status');
            $table->timestamp('responded_at')->nullable()->after('invited_at');
            $table->timestamp('expired_at')->nullable()->after('responded_at');
        });
    }

    public function down(): void
    {
        Schema::connection('user')->table('room_users', function (Blueprint $table) {
            $table->dropColumn(['status', 'invited_at', 'responded_at', 'expired_at']);
        });
    }
};