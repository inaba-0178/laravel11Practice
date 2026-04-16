<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $connection = 'user';

    public function up(): void
    {
        Schema::connection('user')->create('stk_dealer_staffs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('dealer_id')->comment('販売店ID');
            $table->unsignedBigInteger('user_id')->nullable()->comment('ユーザーID');
            $table->string('name', 100)->comment('スタッフ名');
            $table->string('position', 100)->nullable()->comment('役職');
            $table->string('image_path', 500)->nullable()->comment('MinIOパス');
            $table->text('comment')->nullable()->comment('自己紹介・コメント');
            $table->integer('sort_order')->default(0)->comment('表示順');
            $table->boolean('is_active')->default(true)->comment('サイト側表示フラグ');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('dealer_id')->references('id')->on('stk_car_dealers')->onDelete('cascade');
            $table->index('dealer_id', 'idx_dealer_id');
            $table->index('user_id', 'idx_user_id');
        });
    }

    public function down(): void
    {
        Schema::connection('user')->dropIfExists('stk_dealer_staffs');
    }
};