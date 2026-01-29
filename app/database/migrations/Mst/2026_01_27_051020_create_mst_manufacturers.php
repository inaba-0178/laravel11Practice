<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mst_manufacturers', function (Blueprint $table) {
            $table->bigIncrements('id');  // BIGINT UNSIGNED AUTO_INCREMENT
            $table->string('name', 255);
            $table->string('name_kana', 255)->nullable();
            $table->string('display_name', 255)->nullable();
            $table->string('code', 50)->unique();
            $table->string('url', 500)->nullable();
            $table->text('description')->nullable();
            $table->char('country_code', 2)->nullable();
            $table->unsignedInteger('sort_order')->default(1000);
            $table->tinyInteger('is_active')->default(1);
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
            $table->softDeletes();  // deleted_at

            $table->index(['is_active']);
            $table->index(['sort_order']);
            $table->index(['display_name']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mst_manufacturers');
    }
};
