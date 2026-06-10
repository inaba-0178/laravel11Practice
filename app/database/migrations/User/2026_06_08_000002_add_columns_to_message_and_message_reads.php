<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $connection = 'user';

    public function up(): void
    {
        Schema::connection('user')->table('messages', function (Blueprint $table) {
            $table->dropForeign('messages_user_id_foreign');
            $table->string('user_id', 36)->change();
            $table->enum('user_type', ['staff', 'member'])->default('member')->after('user_id');
        });

        Schema::connection('user')->table('message_reads', function (Blueprint $table) {
            $table->dropForeign('message_reads_user_id_foreign');
            $table->string('user_id', 36)->change();
            $table->enum('user_type', ['staff', 'member'])->default('member')->after('user_id');
        });
    }

    public function down(): void
    {
        Schema::connection('user')->table('messages', function (Blueprint $table) {
            $table->dropColumn('user_type');
            $table->unsignedBigInteger('user_id')->change();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        Schema::connection('user')->table('message_reads', function (Blueprint $table) {
            $table->dropColumn('user_type');
            $table->unsignedBigInteger('user_id')->change();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }
};