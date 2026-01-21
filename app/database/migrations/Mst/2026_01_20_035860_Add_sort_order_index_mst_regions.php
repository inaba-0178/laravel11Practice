<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $connection = 'mst';
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::connection('mst')->table('mst_regions', function (Blueprint $table) {
            $table->index('sort_order');  // ← インデックス追加
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::connection('mst')->table('mst_regions', function (Blueprint $table) {
            $table->dropIndex(['sort_order']);
        });
    }
};
