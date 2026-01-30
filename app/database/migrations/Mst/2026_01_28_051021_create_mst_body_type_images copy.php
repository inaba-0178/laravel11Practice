<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mst_body_type_images', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('body_type_id');
            $table->string('image_type', 50)->default('logo');
            $table->string('file_path', 500);
            $table->string('alt_text', 255)->nullable();
            $table->unsignedInteger('sort_order')->default(1000);
            $table->tinyInteger('is_main')->default(0);
            $table->tinyInteger('is_active')->default(1);
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();
            $table->softDeletes();

            $table->index(['body_type_id']);
            $table->foreign('body_type_id')
                  ->references('id')
                  ->on('mst_body_types')
                  ->onUpdate('CASCADE')
                  ->onDelete('RESTRICT');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mst_body_type_images');
    }
};
