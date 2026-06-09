<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $connection = 'user';

    public function up(): void
    {
        Schema::connection('user')->table('rooms', function (Blueprint $table) {
            $table->string('related_type')->nullable()->comment('紐づくタイプ inquiry/car_qa/dealer_internal')->after('type');
            $table->unsignedBigInteger('related_id')->nullable()->comment('紐づくID')->after('related_type');
            $table->tinyInteger('is_active')->default(1)->comment('有効フラグ')->after('related_id');
        });
    }

    public function down(): void
    {
        Schema::connection('user')->table('rooms', function (Blueprint $table) {
            $table->dropColumn(['related_type', 'related_id', 'is_active']);
        });
    }
};