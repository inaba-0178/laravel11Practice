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
        Schema::connection('user')->table('stk_cars', function (Blueprint $table) {
            $table->unsignedBigInteger('bulk_batch_id')
                ->nullable()
                ->after('bulk_upload_key')
                ->comment('一括アップロードバッチID（一括登録時のみセット）');

            $table->index('bulk_batch_id', 'idx_bulk_batch_id');

            $table->foreign('bulk_batch_id')
                ->references('id')
                ->on('stk_bulk_upload_batches')
                ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::connection('user')->table('stk_cars', function (Blueprint $table) {
            $table->dropForeign('stk_cars_bulk_batch_id_foreign');
            $table->dropIndex('idx_bulk_batch_id');
            $table->dropColumn('bulk_batch_id');
        });
    }
};