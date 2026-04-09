<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $connection = 'user';

    public function up(): void
    {
        Schema::connection('user')->create('stk_dealer_fees', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('dealer_id');
            $table->string('name')->comment('プラン名（例：国産車・輸入車）');
            $table->boolean('is_default')->default(false)->comment('デフォルトプラン');
            $table->decimal('registration_fee', 10, 0)->default(0)->comment('登録・手続き代行費用（円）');
            $table->decimal('garage_cert_fee', 10, 0)->default(0)->comment('車庫証明費用（円）');
            $table->decimal('delivery_fee', 10, 0)->default(0)->comment('納車費用（円）');
            $table->decimal('maintenance_fee', 10, 0)->default(0)->comment('整備費用（円）');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('dealer_id')->references('id')->on('stk_car_dealers');
        });
    }

    public function down(): void
    {
        Schema::connection('user')->dropIfExists('stk_dealer_fees');
    }
};