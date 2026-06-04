<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $connection = 'mst';

    public function up(): void
    {
        Schema::connection('mst')->create('opr_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key', 100)->unique()->comment('設定キー');
            $table->string('value', 255)->comment('設定値');
            $table->string('label', 255)->comment('管理画面表示名');
            $table->text('description')->nullable()->comment('説明');
            $table->timestamps();
        });

        // 初期データ
        DB::connection('mst')->table('opr_settings')->insert([
            [
                'key'         => 'view_count_delay_seconds',
                'value'       => '5',
                'label'       => '閲覧カウント待機秒数',
                'description' => 'ページ表示後何秒で閲覧数をカウントするか',
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'key'         => 'view_count_interval_minutes',
                'value'       => '60',
                'label'       => '閲覧カウント間隔（分）',
                'description' => '同一ユーザーの再カウントまでの時間',
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
        ]);
    }

    public function down(): void
    {
        Schema::connection('mst')->dropIfExists('opr_settings');
    }
};