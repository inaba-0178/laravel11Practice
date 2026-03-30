<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $connection = 'user';

    public function up(): void
    {
        Schema::connection($this->connection)->table('stk_cars', function (Blueprint $table) {
            $table->text('rejection_reason')->nullable()->comment('差し戻し理由')->after('deleted_at');
        });
    }

    public function down(): void
    {
        Schema::connection($this->connection)->table('stk_cars', function (Blueprint $table) {
            $table->dropColumn('rejection_reason');
        });
    }
};