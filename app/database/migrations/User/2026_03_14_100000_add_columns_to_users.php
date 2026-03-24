<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedBigInteger('dealer_id')
                  ->nullable()
                  ->comment('担当ディーラーID（dealer/dealer_staffロールのみ）')
                  ->after('id');
            $table->enum('role', ['super', 'admin', 'dealer', 'dealer_staff'])
                  ->default('dealer')
                  ->comment('ロール')
                  ->after('dealer_id');
            $table->boolean('is_active')
                  ->default(true)
                  ->comment('有効フラグ')
                  ->after('role');
            $table->boolean('is_public')
                  ->default(false)
                  ->comment('ディーラーページ公開フラグ')
                  ->after('is_active');
            $table->integer('display_order')
                  ->default(0)
                  ->comment('表示順')
                  ->after('is_public');
            $table->string('profile_image_url', 500)
                  ->nullable()
                  ->comment('顔写真URL')
                  ->after('name');
            $table->string('position', 100)
                  ->nullable()
                  ->comment('役職・肩書き')
                  ->after('profile_image_url');
            $table->text('bio')
                  ->nullable()
                  ->comment('自己紹介・一言コメント')
                  ->after('position');
            $table->string('specialty', 255)
                  ->nullable()
                  ->comment('担当業務（接客・整備など）')
                  ->after('bio');
            $table->date('joined_at')
                  ->nullable()
                  ->comment('入社日（在籍年数の自動計算用）')
                  ->after('specialty');
            $table->softDeletes()->after('updated_at');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'dealer_id',
                'role',
                'is_active',
                'is_public',
                'display_order',
                'profile_image_url',
                'position',
                'bio',
                'specialty',
                'joined_at',
                'deleted_at',
            ]);
        });
    }
};