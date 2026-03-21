<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $connection = 'user';

    public function up(): void
    {
        Schema::connection('user')->table('stk_reservations', function (Blueprint $table) {
            // member_idをNULL許容に変更（ゲスト対応）
            $table->string('member_id', 36)->nullable()->change();

            // ゲスト情報カラム追加
            $table->string('guest_name', 100)->nullable()->comment('ゲスト名')->after('memo');
            $table->string('guest_phone', 20)->nullable()->comment('ゲスト電話番号')->after('guest_name');
            $table->string('guest_email', 255)->nullable()->comment('ゲストメールアドレス')->after('guest_phone');
            $table->string('guest_address', 255)->nullable()->comment('ゲスト住所')->after('guest_email');
        });
    }

    public function down(): void
    {
        Schema::connection('user')->table('stk_reservations', function (Blueprint $table) {
            $table->dropColumn(['guest_name', 'guest_phone', 'guest_email', 'guest_address']);
            $table->string('member_id', 36)->nullable(false)->change();
        });
    }
};