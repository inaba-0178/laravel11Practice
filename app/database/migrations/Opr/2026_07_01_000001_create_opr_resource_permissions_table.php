<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $connection = 'mst';

    public function up(): void
    {
        Schema::connection('mst')->create('opr_resource_permissions', function (Blueprint $table) {
            $table->id();
            $table->string('resource_key')->unique()->comment('Resource/Pageのクラス名');
            $table->string('resource_label')->comment('管理画面表示名');
            $table->string('resource_group')->comment('グループ（マスタ参照・ディーラー機能等）');
            $table->json('allowed_roles')->comment('アクセス許可ロール一覧');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::connection('mst')->dropIfExists('opr_resource_permissions');
    }
};
