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
            $table->enum('status', [
                'draft',
                'pending',
                'approved_pending',
                'scheduled',
                'available',
                'reserved',
                'sold',
                'rejected',
                'deleted',
            ])->default('draft')->comment('在庫管理')->change();
        });
    }

    public function down(): void
    {
        Schema::connection('user')->table('stk_cars', function (Blueprint $table) {
            $table->enum('status', [
                'draft',
                'pending',
                'approved_pending',
                'available',
                'reserved',
                'sold',
                'rejected',
                'deleted',
            ])->default('draft')->comment('在庫管理')->change();
        });
    }
};