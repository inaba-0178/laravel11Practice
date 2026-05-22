<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $connection = 'user';

    public function up(): void
    {
        Schema::connection('user')->table('stk_dealer_schedules', function (Blueprint $table) {
            $table->tinyInteger('is_closed')
                ->default(0)
                ->after('is_available')
                ->comment('定休日フラグ（1=定休日）');
        });
    }

    public function down(): void
    {
        Schema::connection('user')->table('stk_dealer_schedules', function (Blueprint $table) {
            $table->dropColumn('is_closed');
        });
    }
};