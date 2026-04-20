<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $connection = 'user';

    public function up(): void
    {
        Schema::connection('user')->table('usr_users', function (Blueprint $table) {
            $table->string('nickname', 50)->nullable()->comment('ニックネーム')->after('mei_kana');
        });
    }

    public function down(): void
    {
        Schema::connection('user')->table('usr_users', function (Blueprint $table) {
            $table->dropColumn('nickname');
        });
    }
};