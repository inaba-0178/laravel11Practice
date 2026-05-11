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
            $table->string('color_group', 20)->nullable()->comment('色系統')->after('color');
        });
    }

    public function down(): void
    {
        Schema::connection('user')->table('stk_cars', function (Blueprint $table) {
            $table->dropColumn('color_group');
        });
    }
};