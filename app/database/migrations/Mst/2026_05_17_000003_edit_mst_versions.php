<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $connection = 'mst';

    public function up(): void
    {
        Schema::connection('mst')->table('mst_versions', function (Blueprint $table) {
            $table->foreignId('rolled_back_by')->nullable()->after('activated_at');
            $table->timestamp('rolled_back_at')->nullable()->after('rolled_back_by');
            $table->text('rollback_reason')->nullable()->after('rolled_back_at');
        });
    }

    public function down(): void
    {
        Schema::connection('mst')->table('mst_versions', function (Blueprint $table) {
            $table->dropColumn(['rolled_back_by', 'rolled_back_at', 'rollback_reason']);
        });
    }
};