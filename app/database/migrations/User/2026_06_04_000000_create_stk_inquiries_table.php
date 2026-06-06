<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $connection = 'user';

    public function up(): void
    {
        Schema::connection('user')->create('stk_inquiries', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('dealer_id')->comment('ディーラーID');
            $table->unsignedBigInteger('car_id')->comment('車両ID');
            $table->string('member_id', 36)->nullable()->comment('会員ID（未ログイン時はNULL）');
            $table->enum('status', ['new', 'replied', 'closed', 'phone_replied'])->default('new')->comment('ステータス');
            $table->enum('inquiry_type', [
                'stock_check',
                'estimate',
                'condition_check',
                'other'
            ])->default('stock_check')->comment('問い合わせ種別');
            $table->string('name', 100)->nullable()->comment('名前');
            $table->string('phone', 20)->nullable()->comment('電話番号');
            $table->string('email', 255)->nullable()->comment('メールアドレス');
            $table->string('postal_code', 8)->nullable()->comment('郵便番号');
            $table->string('address', 255)->nullable()->comment('住所');
            $table->text('message')->nullable()->comment('問い合わせ内容');
            $table->text('reply')->nullable()->comment('ディーラー返答');
            $table->timestamp('replied_at')->nullable()->comment('返答日時');
            $table->unsignedBigInteger('replied_by')->nullable()->comment('返答者');
            $table->timestamps();
            $table->softDeletes();

            $table->index('dealer_id', 'idx_dealer_id');
            $table->index('car_id', 'idx_car_id');
            $table->index('member_id', 'idx_member_id');
            $table->index('status', 'idx_status');

            $table->foreign('dealer_id')
                ->references('id')
                ->on('stk_car_dealers')
                ->onDelete('cascade');

            $table->foreign('car_id')
                ->references('id')
                ->on('stk_cars')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::connection('user')->dropIfExists('stk_inquiries');
    }
};