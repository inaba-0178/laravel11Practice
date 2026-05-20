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
            $table->string('bulk_upload_key', 100)
                ->nullable()
                ->after('dealer_fee_id')
                ->comment('一括アップロード識別キー（一括登録時のみセット）');

            $table->index('bulk_upload_key', 'idx_bulk_upload_key');
        });
    }

    public function down(): void
    {
        Schema::connection('user')->table('stk_cars', function (Blueprint $table) {
            $table->dropIndex('idx_bulk_upload_key');
            $table->dropColumn('bulk_upload_key');
        });
    }
};