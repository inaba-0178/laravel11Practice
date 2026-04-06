<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    protected $connection = 'user';

    public function up(): void
    {
        // 1. dealer_loan_plan_idの外部キー制約を削除
        Schema::connection($this->connection)->table('stk_car_loans', function (Blueprint $table) {
            $table->string('dealer_loan_plan_id', 36)->nullable()->change();
        });

        // 2. stk_dealer_loan_plansのidをUUIDに変更
        // 既存データを一時退避
        $plans = \DB::connection('user')->table('stk_dealer_loan_plans')->get();

        // 既存テーブルを削除して再作成
        Schema::connection($this->connection)->dropIfExists('stk_dealer_loan_plans');

        Schema::connection($this->connection)->create('stk_dealer_loan_plans', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId('dealer_id')->constrained('stk_car_dealers')->cascadeOnDelete();
            $table->string('name')->comment('プラン名');
            $table->decimal('interest_rate', 5, 2)->comment('金利（%）');
            $table->json('months_options')->comment('回数選択肢');
            $table->unsignedInteger('min_months')->comment('最小回数');
            $table->unsignedInteger('max_months')->comment('最大回数');
            $table->decimal('bonus_amount', 10, 0)->nullable()->comment('ボーナス加算額（円）');
            $table->unsignedInteger('bonus_times')->nullable()->comment('ボーナス回数（年）');
            $table->tinyInteger('is_active')->default(1)->comment('有効/無効');
            $table->softDeletes();
            $table->foreignId('delete_requested_by')->nullable()->comment('削除申請した担当者user_id');
            $table->text('delete_request_reason')->nullable()->comment('削除申請理由');
            $table->timestamp('delete_requested_at')->nullable()->comment('削除申請日時');
            $table->foreignId('delete_approved_by')->nullable()->comment('削除承認した管理者user_id');
            $table->timestamp('delete_approved_at')->nullable()->comment('削除承認日時');
            $table->timestamps();
        });

        // 既存データをUUIDで再挿入
        foreach ($plans as $plan) {
            \DB::connection('user')->table('stk_dealer_loan_plans')->insert([
                'id'             => Str::uuid()->toString(),
                'dealer_id'      => $plan->dealer_id,
                'name'           => $plan->name,
                'interest_rate'  => $plan->interest_rate,
                'months_options' => $plan->months_options,
                'min_months'     => $plan->min_months,
                'max_months'     => $plan->max_months,
                'bonus_amount'   => $plan->bonus_amount,
                'bonus_times'    => $plan->bonus_times,
                'is_active'      => $plan->is_active,
                'created_at'     => $plan->created_at,
                'updated_at'     => $plan->updated_at,
            ]);
        }
    }

    public function down(): void
    {
        // 戻す場合は手動対応
    }
};