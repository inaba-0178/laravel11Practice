<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $connection = 'user';

    public function up(): void
    {
        Schema::connection('user')->create('stk_affiliated_stores', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('dealer_id')->comment('申請元ディーラーID');
            $table->unsignedBigInteger('affiliated_dealer_id')->comment('申請先ディーラーID');
            $table->enum('type', ['affiliated', 'partner'])->comment('種別：系列店/提携店');
            $table->enum('status', ['pending', 'approved', 'rejected', 'dissolved'])->default('pending')->comment('ステータス');
            $table->unsignedBigInteger('requested_by')->comment('申請したuser_id');
            $table->timestamp('requested_at')->nullable()->comment('申請日時');
            $table->unsignedBigInteger('approved_by')->nullable()->comment('承認したuser_id');
            $table->timestamp('approved_at')->nullable()->comment('承認日時');
            $table->string('rejected_reason', 255)->nullable()->comment('拒否理由');
            $table->unsignedBigInteger('dissolved_by')->nullable()->comment('解除したuser_id');
            $table->timestamp('dissolved_at')->nullable()->comment('解除日時');
            $table->string('dissolved_reason', 255)->nullable()->comment('解除理由');
            $table->integer('sort_order')->default(0)->comment('表示順');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('dealer_id')->references('id')->on('stk_car_dealers')->onDelete('cascade');
            $table->foreign('affiliated_dealer_id')->references('id')->on('stk_car_dealers')->onDelete('cascade');

            $table->index('dealer_id', 'idx_dealer_id');
            $table->index('affiliated_dealer_id', 'idx_affiliated_dealer_id');
            $table->index('status', 'idx_status');

            // 同じ組み合わせの重複防止
            $table->unique(['dealer_id', 'affiliated_dealer_id'], 'uq_dealer_affiliated');
        });
    }

    public function down(): void
    {
        Schema::connection('user')->dropIfExists('stk_affiliated_stores');
    }
};