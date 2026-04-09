<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $connection = 'user';

    public function up(): void
    {
        Schema::connection($this->connection)->table('stk_cars', function (Blueprint $table) {
            $table->unsignedBigInteger('dealer_fee_id')
                ->nullable()
                ->after('recycle_fee')
                ->comment('ディーラー諸費用プランID');

            $table->foreign('dealer_fee_id')
                ->references('id')
                ->on('stk_dealer_fees')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::connection($this->connection)->table('stk_cars', function (Blueprint $table) {
            $table->dropForeign(['dealer_fee_id']);
            $table->dropColumn('dealer_fee_id');
        });
    }
};