<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::connection('mst')->create('mst_countries', function (Blueprint $table) {
            $table->id();
            $table->char('country_code', 2)->comment('国コード（ISO 3166-1 alpha-2）');
            $table->string('label', 50)->comment('国名（日本語）');
            $table->string('flag', 10)->comment('国旗絵文字');
            $table->string('anchor', 50)->comment('アンカー名');
            $table->unsignedInteger('sort_order')->default(1000);
            $table->tinyInteger('is_active')->default(1);
            $table->timestamps();

            $table->unique('country_code');
            $table->index('sort_order');
            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::connection('mst')->dropIfExists('mst_countries');
    }
};