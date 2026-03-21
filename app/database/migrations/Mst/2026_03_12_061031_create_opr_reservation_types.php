<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $connection = 'mst';

    public function up(): void
    {
        if (!Schema::connection('mst')->hasTable('opr_reservation_types')) {
            Schema::connection('mst')->create('opr_reservation_types', function (Blueprint $table) {
                $table->id();
                $table->string('name', 100)->comment('予約種別名');
                $table->string('code', 50)->unique()->comment('予約種別コード');
                $table->string('description', 255)->nullable()->comment('説明');
                $table->tinyInteger('is_active')->default(1)->comment('有効フラグ');
                $table->integer('sort_order')->default(0)->comment('表示順');
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::connection('mst')->dropIfExists('opr_reservation_types');
    }
};