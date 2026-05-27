<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $connection = 'mst';

    public function up(): void
    {
        Schema::connection('mst')->create('opr_main_views', function (Blueprint $table) {
            $table->id();
            $table->string('title', 255)->nullable()->comment('スライドタイトル');
            $table->string('sub', 500)->nullable()->comment('サブテキスト');
            $table->string('label', 255)->nullable()->comment('ラベル');
            $table->string('image_path', 255)->nullable()->comment('画像パス(S3)');
            $table->string('link_url', 255)->nullable()->comment('クリック時のリンク先');
            $table->unsignedInteger('sort_order')->default(1000)->comment('表示順');
            $table->tinyInteger('is_active')->default(1)->comment('有効フラグ');
            $table->timestamp('start_at')->nullable()->comment('表示開始日時');
            $table->timestamp('end_at')->nullable()->comment('表示終了日時');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::connection('mst')->dropIfExists('opr_main_views');
    }
};