<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $connection = 'mst';

    public function up(): void
    {
        Schema::connection($this->connection)->create('mst_loan_plans', function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('プラン名 例：標準プラン');
            $table->decimal('interest_rate', 5, 2)->comment('金利（%）');
            $table->json('months_options')->comment('回数選択肢 例：[12,24,36,48,60,120]');
            $table->unsignedInteger('min_months')->comment('最小回数');
            $table->unsignedInteger('max_months')->comment('最大回数');
            $table->tinyInteger('is_default')->default(0)->comment('システムデフォルトフラグ');
            $table->tinyInteger('is_active')->default(1)->comment('有効/無効');
            $table->timestamps();
        });

        // デフォルトデータ挿入
        \DB::connection('mst')->table('mst_loan_plans')->insert([
            [
                'name'           => '標準プラン',
                'interest_rate'  => 3.9,
                'months_options' => json_encode([12, 24, 36, 48, 60, 120]),
                'min_months'     => 12,
                'max_months'     => 120,
                'is_default'     => 1,
                'is_active'      => 1,
                'created_at'     => now(),
                'updated_at'     => now(),
            ],
        ]);
    }

    public function down(): void
    {
        Schema::connection($this->connection)->dropIfExists('mst_loan_plans');
    }
};