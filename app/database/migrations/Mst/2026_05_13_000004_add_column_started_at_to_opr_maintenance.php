<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $connection = 'mst';

    public function up(): void
    {
        Schema::connection('mst')->table('opr_maintenance', function (Blueprint $table) {
            $table->timestamp('started_at')->nullable()->comment('開始予定時刻')->after('is_maintenance');
        });
    }

    public function down(): void
    {
        Schema::connection('mst')->table('opr_maintenance', function (Blueprint $table) {
            $table->dropColumn('started_at');
        });
    }
};