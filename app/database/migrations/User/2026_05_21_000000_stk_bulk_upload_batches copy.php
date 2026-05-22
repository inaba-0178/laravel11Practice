<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $connection = 'user';

    public function up(): void
    {
        Schema::connection('user')->create('stk_bulk_upload_batches', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('dealer_id')->comment('ディーラーID');
            $table->unsignedBigInteger('uploaded_by')->comment('アップロードしたユーザーID');
            $table->timestamp('uploaded_at')->comment('アップロード日時');
            $table->timestamp('approved_at')->nullable()->comment('全車両承認完了日時');
            $table->integer('total_count')->default(0)->comment('登録台数');
            $table->integer('create_count')->default(0)->comment('新規台数');
            $table->integer('update_count')->default(0)->comment('更新台数');
            $table->integer('delete_count')->default(0)->comment('削除台数');
            $table->integer('approved_count')->default(0)->comment('承認済み台数');
            $table->integer('rejected_count')->default(0)->comment('差し戻し台数');
            $table->integer('pending_count')->default(0)->comment('承認待ち台数');
            $table->timestamps();

            $table->index('dealer_id', 'idx_dealer_id');
            $table->index('uploaded_at', 'idx_uploaded_at');
            $table->foreign('dealer_id')
                ->references('id')
                ->on('stk_car_dealers')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::connection('user')->dropIfExists('stk_bulk_upload_batches');
    }
};