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
            $table->decimal('recycle_fee', 10, 0)->nullable()->after('price')->comment('リサイクル預託金（円）');
        });
    }

    public function down(): void
    {
        Schema::connection('user')->table('stk_cars', function (Blueprint $table) {
            $table->dropColumn('recycle_fee');
        });
    }
};