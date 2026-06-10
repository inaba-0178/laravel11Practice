<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $connection = 'user';

    public function up(): void
    {
        Schema::connection('user')->create('room_user_deletion_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('room_id');
            $table->string('deleted_user_id', 36);
            $table->enum('deleted_user_type', ['staff', 'member']);
            $table->string('deleted_by_id', 36);
            $table->enum('deleted_by_type', ['staff', 'member']);
            $table->enum('reason', ['担当者交代', 'クレーム対応', '誤追加', 'その他']);
            $table->text('reason_detail')->nullable();
            $table->timestamps();

            $table->foreign('room_id')->references('id')->on('rooms')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::connection('user')->dropIfExists('room_user_deletion_logs');
    }
};