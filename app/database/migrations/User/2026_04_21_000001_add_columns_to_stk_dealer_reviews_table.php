<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $connection = 'user';

    public function up(): void
    {
        Schema::connection('user')->table('stk_dealer_reviews', function (Blueprint $table) {
            // member_idをNULL許可に変更（ゲスト投稿対応）
            $table->char('member_id', 36)->nullable()->comment('会員ID')->change();

            // カラム追加
            $table->string('nickname', 20)->nullable()->comment('ニックネーム')->after('member_id');
            $table->string('guest_name', 20)->nullable()->comment('ゲスト氏名')->after('comment');
            $table->string('guest_phone', 11)->nullable()->comment('ゲスト電話番号')->after('guest_name');
            $table->string('guest_email', 100)->nullable()->comment('ゲストメールアドレス')->after('guest_phone');
        });
    }

    public function down(): void
    {
        Schema::connection('user')->table('stk_dealer_reviews', function (Blueprint $table) {
            $table->char('member_id', 36)->nullable(false)->comment('会員ID')->change();
            $table->dropColumn(['nickname', 'guest_name', 'guest_phone', 'guest_email']);
        });
    }
};