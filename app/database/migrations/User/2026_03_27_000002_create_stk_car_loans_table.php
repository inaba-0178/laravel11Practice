<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $connection = 'user';

    public function up(): void
    {
        Schema::connection($this->connection)->create('stk_car_loans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('car_id')->constrained('stk_cars')->cascadeOnDelete();
            $table->enum('loan_type', ['standard', 'residual'])->default('standard')->comment('standard=通常ローン residual=残価設定');
            $table->decimal('interest_rate', 5, 2)->nullable()->comment('金利（NULLならシステムデフォルト）');
            $table->unsignedInteger('loan_months')->nullable()->comment('ローン期間（月）');
            $table->decimal('down_payment', 10, 0)->nullable()->comment('頭金');
            $table->decimal('residual_value', 10, 0)->nullable()->comment('残価（残価設定ローンのみ）');
            $table->decimal('misc_fee', 10, 0)->nullable()->comment('諸費用');
            $table->text('note')->nullable()->comment('備考');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::connection($this->connection)->dropIfExists('stk_car_loans');
    }
};