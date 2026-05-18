<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $connection = 'mst';

    public function up(): void
    {
        Schema::connection('mst')->create('opr_maintenance', function (Blueprint $table) {
            $table->id();
            $table->tinyInteger('is_maintenance')->default(0)->comment('メンテナンスフラグ');
            $table->string('message', 255)->nullable()->comment('メンテナンスメッセージ');
            $table->timestamp('estimated_end_at')->nullable()->comment('終了予定時刻');
            $table->unsignedBigInteger('updated_by')->nullable()->comment('変更したuser_id');
            $table->timestamp('updated_at')->nullable();
        });

        // 初期データ投入
        DB::connection('mst')->table('opr_maintenance')->insert([
            'is_maintenance'   => 0,
            'message'          => null,
            'estimated_end_at' => null,
            'updated_by'       => null,
            'updated_at'       => now(),
        ]);
    }

    public function down(): void
    {
        Schema::connection('mst')->dropIfExists('opr_maintenance');
    }
};