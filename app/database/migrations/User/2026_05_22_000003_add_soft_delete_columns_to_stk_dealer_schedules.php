<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * user-dbに対して実行する
     */
    protected $connection = 'user';

    public function up(): void
    {
        Schema::connection('user')->table('stk_dealer_schedules', function (Blueprint $table) {
            // ソフトデリート
            $table->softDeletes()->after('updated_at');

            // 削除理由（ディーラー側のみ閲覧・ユーザーには非表示）
            $table->string('delete_reason', 255)
                ->nullable()
                ->after('deleted_at')
                ->comment('削除理由（ディーラー管理用）');
        });
    }

    public function down(): void
    {
        Schema::connection('user')->table('stk_dealer_schedules', function (Blueprint $table) {
            $table->dropSoftDeletes();
            $table->dropColumn('delete_reason');
        });
    }
};