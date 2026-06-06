<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $connection = 'user';

    public function up(): void
    {
        Schema::connection('user')->table('stk_estimates', function (Blueprint $table) {
            $table->enum('discount_type', ['tax_excluded', 'tax_included'])
                ->default('tax_excluded')
                ->comment('値引き種別')
                ->after('discount');
        });
    }

    public function down(): void
    {
        Schema::connection('user')->table('stk_estimates', function (Blueprint $table) {
            $table->dropColumn('discount_type');
        });
    }
};