<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $connection = 'user';

    public function up(): void
    {
        Schema::connection($this->connection)->table('stk_car_loans', function (Blueprint $table) {
            // ===== スナップショット（プラン選択時にコピー） =====
            $table->foreignId('dealer_loan_plan_id')->nullable()->comment('参照元ディーラープランID')->after('car_id');
            $table->string('snapshot_plan_name')->nullable()->comment('選択時プラン名')->after('dealer_loan_plan_id');
            $table->decimal('snapshot_rate', 5, 2)->nullable()->comment('選択時金利')->after('snapshot_plan_name');
            $table->json('snapshot_months_options')->nullable()->comment('選択時回数選択肢')->after('snapshot_rate');
            $table->unsignedInteger('snapshot_min_months')->nullable()->comment('選択時最小回数')->after('snapshot_months_options');
            $table->unsignedInteger('snapshot_max_months')->nullable()->comment('選択時最大回数')->after('snapshot_min_months');
            $table->decimal('snapshot_bonus_amount', 10, 0)->nullable()->comment('選択時ボーナス加算額')->after('snapshot_min_months');
            $table->unsignedInteger('snapshot_bonus_times')->nullable()->comment('選択時ボーナス回数')->after('snapshot_bonus_amount');

            // ===== 契約確定情報（契約処理時にコピー・以降変動なし） =====
            $table->tinyInteger('is_contracted')->default(0)->comment('契約確定フラグ')->after('snapshot_bonus_times');
            $table->timestamp('contracted_at')->nullable()->comment('契約確定日時')->after('is_contracted');
            $table->string('contracted_plan_name')->nullable()->comment('契約時プラン名')->after('contracted_at');
            $table->decimal('contracted_rate', 5, 2)->nullable()->comment('契約時金利')->after('contracted_plan_name');
            $table->unsignedInteger('contracted_months')->nullable()->comment('契約時回数')->after('contracted_rate');
            $table->decimal('contracted_monthly_amount', 10, 0)->nullable()->comment('契約時月々支払額')->after('contracted_months');
            $table->decimal('contracted_bonus_amount', 10, 0)->nullable()->comment('契約時ボーナス加算額')->after('contracted_monthly_amount');
            $table->unsignedInteger('contracted_bonus_times')->nullable()->comment('契約時ボーナス回数')->after('contracted_bonus_amount');
        });
    }

    public function down(): void
    {
        Schema::connection($this->connection)->table('stk_car_loans', function (Blueprint $table) {
            $table->dropColumn([
                'dealer_loan_plan_id',
                'snapshot_plan_name',
                'snapshot_rate',
                'snapshot_months_options',
                'snapshot_min_months',
                'snapshot_max_months',
                'snapshot_bonus_amount',
                'snapshot_bonus_times',
                'is_contracted',
                'contracted_at',
                'contracted_plan_name',
                'contracted_rate',
                'contracted_months',
                'contracted_monthly_amount',
                'contracted_bonus_amount',
                'contracted_bonus_times',
            ]);
        });
    }
};