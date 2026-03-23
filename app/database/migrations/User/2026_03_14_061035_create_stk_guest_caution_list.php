<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::connection('user')->create('stk_guest_caution_list', function (Blueprint $table) {
            $table->id();
            $table->string('email', 255)->nullable()->comment('メールアドレス');
            $table->string('phone', 20)->nullable()->comment('電話番号');
            $table->enum('level', ['caution', 'blacklist'])->comment('警戒レベル');
            $table->text('reason')->nullable()->comment('登録理由');
            $table->unsignedBigInteger('registered_by')->nullable()->comment('登録した管理者ID');
            $table->timestamps();

            $table->index('email');
            $table->index('phone');
        });
    }

    public function down(): void
    {
        Schema::connection('user')->dropIfExists('stk_guest_caution_list');
    }
};