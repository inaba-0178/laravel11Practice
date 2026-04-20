<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $connection = 'user';

    public function up(): void
    {
        Schema::connection('user')->create('stk_dealer_review_replies', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('review_id')->comment('口コミID');
            $table->unsignedBigInteger('dealer_id')->comment('販売店ID');
            $table->unsignedBigInteger('user_id')->comment('返答したユーザーID');
            $table->string('responder_name', 100)->nullable()->comment('担当者名（管理ツールのみ表示）');
            $table->text('body')->comment('返信本文');
            $table->string('deleted_reason', 255)->nullable()->comment('削除理由');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('review_id')->references('id')->on('stk_dealer_reviews')->onDelete('cascade');
            $table->foreign('dealer_id')->references('id')->on('stk_car_dealers')->onDelete('cascade');

            $table->index('review_id', 'idx_review_id');
            $table->index('dealer_id', 'idx_dealer_id');
        });
    }

    public function down(): void
    {
        Schema::connection('user')->dropIfExists('stk_dealer_review_replies');
    }
};