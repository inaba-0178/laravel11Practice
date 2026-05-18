<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $connection = 'mst';

    public function up(): void
    {
        Schema::connection('mst')->create('mst_versions', function (Blueprint $table) {
            $table->id();
            $table->string('version', 20)->comment('バージョン番号（例：0.0.1）');
            $table->text('description')->nullable()->comment('更新内容の説明');
            $table->enum('status', ['draft', 'pending', 'approved', 'active', 'archived'])
                  ->default('draft');

            // アップロード
            $table->unsignedBigInteger('uploaded_by')->nullable()->comment('アップロードしたuser_id');
            $table->timestamp('uploaded_at')->nullable()->comment('アップロード日時');

            // 承認申請
            $table->unsignedBigInteger('requested_by')->nullable()->comment('承認申請したuser_id');
            $table->timestamp('requested_at')->nullable()->comment('承認申請日時');

            // 承認
            $table->unsignedBigInteger('approved_by')->nullable()->comment('承認したuser_id');
            $table->timestamp('approved_at')->nullable()->comment('承認日時');
            $table->text('rejected_reason')->nullable()->comment('却下理由');

            // 有効化
            $table->unsignedBigInteger('activated_by')->nullable()->comment('有効化したuser_id');
            $table->timestamp('activated_at')->nullable()->comment('有効化日時');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::connection('mst')->dropIfExists('mst_versions');
    }
};