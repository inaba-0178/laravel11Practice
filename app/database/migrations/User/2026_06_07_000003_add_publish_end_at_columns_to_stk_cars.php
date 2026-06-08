<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $connection = 'user';

    public function up(): void
    {
        Schema::connection('user')->table('stk_cars', function (Blueprint $table) {
            $table->timestamp('publish_end_at')->nullable()->comment('公開終了日時')->after('published_at');
        });
    }

    public function down(): void
    {
        Schema::connection('user')->table('stk_cars', function (Blueprint $table) {
            $table->dropColumn('publish_end_at');
        });
    }
};