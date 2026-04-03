<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $connection = 'user';

    public function up(): void
    {
        Schema::connection($this->connection)->table('stk_car_dealers', function (Blueprint $table) {
            $table->tinyInteger('loan_setting_enabled')->default(0)->comment('ローン設定権限フラグ')->after('updated_at');
            $table->foreignId('loan_setting_requested_by')->nullable()->comment('申請した担当者user_id')->after('loan_setting_enabled');
            $table->text('loan_setting_reason')->nullable()->comment('申請理由')->after('loan_setting_requested_by');
            $table->timestamp('loan_setting_requested_at')->nullable()->comment('申請日時')->after('loan_setting_reason');
            $table->foreignId('loan_setting_approved_by')->nullable()->comment('承認した管理者user_id')->after('loan_setting_requested_at');
            $table->timestamp('loan_setting_approved_at')->nullable()->comment('承認日時')->after('loan_setting_approved_by');
            $table->text('loan_setting_rejected_reason')->nullable()->comment('拒否理由')->after('loan_setting_approved_at');
        });
    }

    public function down(): void
    {
        Schema::connection($this->connection)->table('stk_car_dealers', function (Blueprint $table) {
            $table->dropColumn([
                'loan_setting_enabled',
                'loan_setting_requested_by',
                'loan_setting_reason',
                'loan_setting_requested_at',
                'loan_setting_approved_by',
                'loan_setting_approved_at',
                'loan_setting_rejected_reason',
            ]);
        });
    }
};