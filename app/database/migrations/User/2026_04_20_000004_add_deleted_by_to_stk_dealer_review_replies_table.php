<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $connection = 'user';

    public function up(): void
    {
        Schema::connection('user')->table('stk_dealer_review_replies', function (Blueprint $table) {
            $table->unsignedBigInteger('deleted_by')->nullable()->comment('削除したユーザーID')->after('deleted_reason');
        });
    }

    public function down(): void
    {
        Schema::connection('user')->table('stk_dealer_review_replies', function (Blueprint $table) {
            $table->dropColumn('deleted_by');
        });
    }
};