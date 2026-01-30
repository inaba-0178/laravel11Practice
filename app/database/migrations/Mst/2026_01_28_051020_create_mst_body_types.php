<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mst_body_types', function (Blueprint $table) {
            $table->bigIncrements('id');  // BIGINT UNSIGNED AUTO_INCREMENT
            $table->string('name', 255);
            $table->string('name_kana', 255)->nullable();
            $table->string('code', 50)->unique();
            $table->text('description')->nullable();
            $table->json('available_countries')->default('["JP"]');
            $table->unsignedInteger('sort_order')->default(1000);
            $table->tinyInteger('is_active')->default(1);
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
            $table->softDeletes();  // deleted_at

            $table->index(['is_active']);
            $table->index(['sort_order']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mst_body_types');
    }
};
