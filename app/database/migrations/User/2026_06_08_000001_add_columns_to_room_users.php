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
            // 外部キー制約削除
            $table->dropForeign('room_users_user_id_foreign');
            // user_idをVARCHAR(36)に変更
            $table->string('user_id', 36)->change();
            // user_type追加
            $table->enum('user_type', ['staff', 'member'])->default('member')->after('user_id');
        });
    }

    public function down(): void
    {
        Schema::connection('user')->table('room_users', function (Blueprint $table) {
            $table->dropColumn('user_type');
            $table->unsignedBigInteger('user_id')->change();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }
};