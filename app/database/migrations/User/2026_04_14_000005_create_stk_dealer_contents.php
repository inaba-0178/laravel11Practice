<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $connection = 'user';

    public function up(): void
    {
        Schema::connection('user')->create('stk_dealer_contents', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('dealer_id')->comment('販売店ID');
            $table->enum('category', ['service', 'event', 'warranty'])->comment('カテゴリ');
            $table->string('title', 255)->comment('タイトル');
            $table->text('description')->nullable()->comment('概要・説明');
            $table->string('image_path', 500)->nullable()->comment('MinIOパス');
            $table->integer('sort_order')->default(0)->comment('表示順');
            $table->boolean('is_active')->default(true)->comment('表示フラグ');
            $table->date('started_at')->nullable()->comment('開始日（イベント用）');
            $table->date('ended_at')->nullable()->comment('終了日（イベント用）');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('dealer_id')->references('id')->on('stk_car_dealers')->onDelete('cascade');
            $table->index('dealer_id', 'idx_dealer_id');
            $table->index('category', 'idx_category');
        });
    }

    public function down(): void
    {
        Schema::connection('user')->dropIfExists('stk_dealer_contents');
    }
};