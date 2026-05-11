<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $connection = 'mst';

    public function up(): void
    {
        Schema::connection('mst')->table('mst_color_options', function (Blueprint $table) {
            $table->string('group', 20)->nullable()->comment('色系統')->after('hex_code');
        });
    }

    public function down(): void
    {
        Schema::connection('mst')->table('mst_color_options', function (Blueprint $table) {
            $table->dropColumn('group');
        });
    }
};