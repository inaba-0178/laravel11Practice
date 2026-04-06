<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $connection = 'user';

    public function up(): void
    {
        Schema::connection($this->connection)->create('stk_dealer_loan_plans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dealer_id')->constrained('stk_car_dealers')->cascadeOnDelete();
            $table->string('name')->comment('プラン名 例：オリコプラン');
            $table->decimal('interest_rate', 5, 2)->comment('金利（%）');
            $table->json('months_options')->comment('回数選択肢 例：[12,24,36,48,60]');
            $table->unsignedInteger('min_months')->comment('最小回数');
            $table->unsignedInteger('max_months')->comment('最大回数');
            $table->decimal('bonus_amount', 10, 0)->nullable()->comment('ボーナス加算額（円）');
            $table->unsignedInteger('bonus_times')->nullable()->comment('ボーナス回数（年）');
            $table->tinyInteger('is_active')->default(1)->comment('有効/無効');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::connection($this->connection)->dropIfExists('stk_dealer_loan_plans');
    }
};