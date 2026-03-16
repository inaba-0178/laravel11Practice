<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $connection = 'mst';

    public function up(): void
    {
        Schema::connection('mst')->create('opr_mail_templates', function (Blueprint $table) {
            $table->id();
            $table->string('template_name', 255)->comment('テンプレート名');
            $table->string('subject', 255)->comment('件名');
            $table->text('body')->comment('本文（独自タグ含む）');
            $table->json('placeholders')->nullable()->comment('使用可能なプレースホルダー一覧');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::connection('mst')->dropIfExists('opr_mail_templates');
    }
};