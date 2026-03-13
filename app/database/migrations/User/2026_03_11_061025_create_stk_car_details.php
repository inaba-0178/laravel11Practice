<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $connection = 'user';

    public function up(): void
    {
        Schema::connection('user')->create('stk_car_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('car_id')
                ->constrained('stk_cars')
                ->cascadeOnDelete()
                ->cascadeOnUpdate()
                ->comment('車両ID');
            $table->date('first_registration_date')->nullable()->comment('初回登録日');
            $table->date('inspection_expire_date')->nullable()->comment('車検満了日');
            $table->enum('inspection_status', ['available', 'none', 'new_car'])->default('available')->comment('車検状態');
            $table->enum('drive_system', ['2WD', '4WD', 'AWD', 'FR', 'FF', 'MR', 'RR'])->nullable()->comment('駆動方式');
            $table->integer('displacement')->comment('排気量(cc)');
            $table->enum('steering_wheel', ['right', 'left'])->default('right')->comment('ハンドル位置');
            $table->tinyInteger('number_of_doors')->comment('ドア数');
            $table->enum('slide_door', ['none', 'right_only', 'both_manual', 'both_power', 'right_power', 'left_power'])->comment('スライドドア');
            $table->tinyInteger('riding_capacity')->nullable()->comment('乗車定員');
            $table->boolean('loan_available')->nullable()->comment('ローン可否');
            $table->text('description')->nullable()->comment('車両説明文');
            $table->text('free_text')->nullable()->comment('フリーワード（検索用）');
            $table->string('seo_title', 255)->nullable()->comment('SEOタイトル');
            $table->string('seo_description', 500)->nullable()->comment('SEO説明文');
            $table->timestamps();

            $table->unique('car_id', 'unique_car');
            $table->index('inspection_expire_date', 'idx_inspection_expire');
            $table->index('drive_system', 'idx_drive_system');
            $table->index('displacement', 'idx_displacement');
        });
    }

    public function down(): void
    {
        Schema::connection('user')->dropIfExists('stk_car_details');
    }
};