<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $connection = 'log';

    public function up(): void
    {
        Schema::connection('log')->create('log_car', function (Blueprint $table) {
            $table->id();
            $table->string('operator_type', 50);
            $table->unsignedBigInteger('operator_id')->nullable();
            $table->string('operator_name', 255)->nullable();
            $table->string('action', 50);
            $table->string('target_table', 100);
            $table->unsignedBigInteger('target_id')->nullable();
            $table->json('before_data')->nullable();
            $table->json('after_data')->nullable();
            $table->enum('result', ['success', 'failed']);
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamp('operation_at');
            $table->timestamp('created_at')->useCurrent();

            $table->index(['target_table', 'target_id']);
            $table->index(['operator_id', 'operator_type']);
            $table->index('operation_at');
        });
    }

    public function down(): void
    {
        Schema::connection('log')->dropIfExists('log_car');
    }
};
