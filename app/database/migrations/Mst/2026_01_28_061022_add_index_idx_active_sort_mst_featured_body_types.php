<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $connection = 'mst';

    public function up(): void
    {
        Schema::connection('mst')->table('mst_featured_body_types', function (Blueprint $table) {
            // is_active, sort_order の順でインデックス追加
            $table->index(['is_active', 'sort_order'], 'idx_active_sort');
        });
    }

    public function down(): void
    {
        Schema::connection('mst')->table('mst_body_types', function (Blueprint $table) {
            $table->dropIndex('idx_active_sort');
        });
    }
};
