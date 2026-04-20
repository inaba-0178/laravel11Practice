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
            // member_idの型変更（外部キーなしなのでそのまま変更）
            $table->dropIndex('idx_member_id');
            $table->char('member_id', 36)->comment('会員ID')->change();
            $table->index('member_id', 'idx_member_id');

            // カラム追加
            $table->unsignedTinyInteger('rating_service')->nullable()->comment('接客評価（1〜5）')->after('rating');
            $table->unsignedTinyInteger('rating_atmosphere')->nullable()->comment('雰囲気評価（1〜5）')->after('rating_service');
            $table->unsignedTinyInteger('rating_after')->nullable()->comment('アフター評価（1〜5）')->after('rating_atmosphere');
            $table->unsignedTinyInteger('rating_quality')->nullable()->comment('品質評価（1〜5）')->after('rating_after');
            $table->string('purchased_car', 255)->nullable()->comment('購入車種')->after('rating_quality');
            $table->string('purchased_at', 7)->nullable()->comment('購入時期（例：2026/04）')->after('purchased_car');
        });
    }

    public function down(): void
    {
        Schema::connection('user')->table('stk_dealer_reviews', function (Blueprint $table) {
            $table->dropColumn([
                'rating_service',
                'rating_atmosphere',
                'rating_after',
                'rating_quality',
                'purchased_car',
                'purchased_at',
            ]);
            $table->dropIndex('idx_member_id');
            $table->unsignedBigInteger('member_id')->comment('会員ID')->change();
            $table->index('member_id', 'idx_member_id');
        });
    }
};