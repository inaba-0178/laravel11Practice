<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $connection = 'user';

    public function up(): void
    {
        Schema::connection($this->connection)->table('stk_dealer_loan_plans', function (Blueprint $table) {
            // 論理削除
            $table->softDeletes()->after('is_active');

            // 削除申請
            $table->foreignId('delete_requested_by')->nullable()->comment('削除申請した担当者user_id')->after('deleted_at');
            $table->text('delete_request_reason')->nullable()->comment('削除申請理由')->after('delete_requested_by');
            $table->timestamp('delete_requested_at')->nullable()->comment('削除申請日時')->after('delete_request_reason');
            $table->foreignId('delete_approved_by')->nullable()->comment('削除承認した管理者user_id')->after('delete_requested_at');
            $table->timestamp('delete_approved_at')->nullable()->comment('削除承認日時')->after('delete_approved_by');
        });
    }

    public function down(): void
    {
        Schema::connection($this->connection)->table('stk_dealer_loan_plans', function (Blueprint $table) {
            $table->dropSoftDeletes();
            $table->dropColumn([
                'delete_requested_by',
                'delete_request_reason',
                'delete_requested_at',
                'delete_approved_by',
                'delete_approved_at',
            ]);
        });
    }
};