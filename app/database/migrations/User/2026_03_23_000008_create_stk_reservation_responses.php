<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::connection('user')->create('stk_reservation_responses', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('reservation_id')->comment('予約ID');
            $table->timestamp('handled_at')->comment('対応日時');
            $table->unsignedBigInteger('handled_by')->comment('対応担当者ID');
            $table->string('customer_name', 100)->comment('お客様名');
            $table->string('customer_phone', 20)->comment('お客様連絡先');
            $table->string('customer_address', 255)->comment('お客様住所');
            $table->text('visit_purpose')->comment('来店目的・内容');
            $table->text('response_content')->comment('対応内容');
            $table->boolean('has_estimate')->default(false)->comment('見積有無');
            $table->decimal('estimate_amount', 12, 0)->nullable()->comment('見積金額');
            $table->decimal('discount_amount', 12, 0)->nullable()->comment('値引き額');
            $table->decimal('miscellaneous_cost', 12, 0)->nullable()->comment('諸費用');
            $table->boolean('has_purchase')->default(false)->nullable()->comment('購入有無');
            $table->unsignedBigInteger('purchase_car_id')->nullable()->comment('購入車両ID');
            $table->enum('payment_method', ['cash', 'loan', 'other'])->nullable()->comment('支払い方法');
            $table->decimal('loan_down_payment', 12, 0)->nullable()->comment('頭金');
            $table->decimal('loan_monthly_amount', 12, 0)->nullable()->comment('月々支払い額');
            $table->unsignedSmallInteger('loan_count')->nullable()->comment('ローン回数');
            $table->date('contract_date')->nullable()->comment('契約日');
            $table->date('delivery_date')->nullable()->comment('納車予定日');
            $table->timestamps();

            $table->foreign('reservation_id')
                  ->references('id')
                  ->on('stk_reservations')
                  ->onDelete('cascade');

            $table->index('reservation_id');
        });
    }

    public function down(): void
    {
        Schema::connection('user')->dropIfExists('stk_reservation_responses');
    }
};