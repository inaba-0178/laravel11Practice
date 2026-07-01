<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $connection = 'mst';

    public function up(): void
    {
        Schema::connection('mst')->create('opr_permission_logs', function (Blueprint $table) {
            $table->id();
            $table->string('resource_key')->comment('対象Resource/Pageのクラス名');
            $table->unsignedBigInteger('changed_by')->comment('変更者ユーザーID');
            $table->string('changed_by_role')->comment('変更者ロール');
            $table->json('before_roles')->comment('変更前ロール一覧');
            $table->json('after_roles')->comment('変更後ロール一覧');
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::connection('mst')->dropIfExists('opr_permission_logs');
    }
};
