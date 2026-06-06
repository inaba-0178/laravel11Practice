<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $connection = 'user';

    public function up(): void
    {
        Schema::connection('user')->create('stk_estimates', function (Blueprint $table) {
            $table->id();
            $table->string('estimate_number', 20)->unique()->comment('見積番号');
            $table->unsignedBigInteger('dealer_id')->comment('ディーラーID');
            $table->unsignedBigInteger('car_id')->comment('車両ID');
            $table->unsignedBigInteger('inquiry_id')->nullable()->comment('問い合わせID');

            // 顧客情報
            $table->string('customer_name', 100)->nullable()->comment('お名前');
            $table->string('customer_nickname', 50)->nullable()->comment('ニックネーム');
            $table->string('customer_phone', 20)->nullable()->comment('電話番号');
            $table->string('customer_postal_code', 8)->nullable()->comment('郵便番号');
            $table->string('customer_address', 255)->nullable()->comment('住所');

            // 価格情報
            $table->decimal('vehicle_price', 12, 0)->comment('車両本体価格');
            $table->decimal('discount', 12, 0)->default(0)->comment('値引き');
            $table->decimal('recycle_fee', 10, 0)->default(0)->comment('リサイクル預託金');
            $table->decimal('weight_tax', 10, 0)->default(0)->comment('重量税');
            $table->decimal('liability_insurance', 10, 0)->default(0)->comment('自賠責保険料');
            $table->decimal('vehicle_tax', 10, 0)->default(0)->comment('自動車税');
            $table->decimal('registration_fee', 10, 0)->default(0)->comment('登録費用');
            $table->decimal('garage_cert_fee', 10, 0)->default(0)->comment('車庫証明手続費用');
            $table->decimal('delivery_fee', 10, 0)->default(0)->comment('納車費用');
            $table->decimal('maintenance_fee', 10, 0)->default(0)->comment('整備費用');

            // 付属品・必要書類（JSON）
            $table->json('accessories')->nullable()->comment('付属品');
            $table->json('documents')->nullable()->comment('必要書類');

            // その他
            $table->text('notes')->nullable()->comment('備考');
            $table->string('pdf_path', 500)->nullable()->comment('S3 PDFパス');
            $table->date('valid_until')->nullable()->comment('有効期限');
            $table->unsignedBigInteger('created_by')->comment('作成者（担当者）');

            $table->timestamps();
            $table->softDeletes();

            $table->index('dealer_id', 'idx_dealer_id');
            $table->index('car_id', 'idx_car_id');
            $table->index('inquiry_id', 'idx_inquiry_id');
            $table->index('estimate_number', 'idx_estimate_number');

            $table->foreign('dealer_id')
                ->references('id')
                ->on('stk_car_dealers')
                ->onDelete('cascade');

            $table->foreign('car_id')
                ->references('id')
                ->on('stk_cars')
                ->onDelete('cascade');

            $table->foreign('inquiry_id')
                ->references('id')
                ->on('stk_inquiries')
                ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::connection('user')->dropIfExists('stk_estimates');
    }
};