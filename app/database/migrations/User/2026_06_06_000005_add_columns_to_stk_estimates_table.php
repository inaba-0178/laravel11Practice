<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    protected $connection = 'user';

    public function up(): void
    {
        Schema::connection('user')->table('stk_estimates', function (Blueprint $table) {
            // 顧客情報
            $table->date('customer_birth_date')->nullable()->comment('生年月日')->after('customer_address');
            $table->string('customer_workplace', 255)->nullable()->comment('勤務先等')->after('customer_birth_date');
            $table->string('customer_contact_phone', 20)->nullable()->comment('連絡先Tel')->after('customer_workplace');

            // 車両情報
            $table->string('vehicle_model', 100)->nullable()->comment('型式')->after('customer_contact_phone');
            $table->string('chassis_number', 100)->nullable()->comment('車台番号')->after('vehicle_model');
            $table->string('registration_number', 100)->nullable()->comment('登録番号')->after('chassis_number');
            $table->boolean('has_service_record')->nullable()->comment('記録簿有無')->after('registration_number');

            // 下取車情報
            $table->string('trade_in_name', 255)->nullable()->comment('下取車名')->after('has_service_record');
            $table->string('trade_in_model_year', 10)->nullable()->comment('下取車年式')->after('trade_in_name');
            $table->date('trade_in_inspection_date')->nullable()->comment('下取車車検日')->after('trade_in_model_year');
            $table->integer('trade_in_mileage')->nullable()->comment('下取車走行距離')->after('trade_in_inspection_date');
            $table->string('trade_in_color', 50)->nullable()->comment('下取車車体色')->after('trade_in_mileage');
            $table->decimal('trade_in_price', 12, 0)->nullable()->comment('下取車価格')->after('trade_in_color');

            // 支払い情報
            $table->decimal('down_payment', 12, 0)->nullable()->comment('頭金/現金/他')->after('trade_in_price');
            $table->decimal('remaining_amount', 12, 0)->nullable()->comment('残金/所要資金')->after('down_payment');
            $table->integer('credit_months')->nullable()->comment('クレジット支払回数')->after('remaining_amount');
            $table->decimal('credit_fee', 12, 0)->nullable()->comment('分割手数料')->after('credit_months');
            $table->decimal('monthly_payment', 10, 0)->nullable()->comment('月払')->after('credit_fee');
            $table->decimal('bonus_payment', 10, 0)->nullable()->comment('賞与払')->after('monthly_payment');

            // 諸費用追加項目
            $table->decimal('environmental_performance_tax', 10, 0)->nullable()->comment('環境性能割')->after('bonus_payment');
            $table->decimal('inspection_registration_fee', 10, 0)->nullable()->comment('検査/登録/届出（課税）')->after('environmental_performance_tax');
            $table->decimal('inspection_registration_fee_exempt', 10, 0)->nullable()->comment('検査/登録/届出（非課税）')->after('inspection_registration_fee');
            $table->decimal('trade_in_handling_fee', 10, 0)->nullable()->comment('下取車諸手続き')->after('inspection_registration_fee_exempt');
            $table->decimal('assessment_fee', 10, 0)->nullable()->comment('査定料')->after('trade_in_handling_fee');
        });
    }

    public function down(): void
    {
        Schema::connection('user')->table('stk_estimates', function (Blueprint $table) {
            $table->dropColumn([
                'customer_birth_date',
                'customer_workplace',
                'customer_contact_phone',
                'vehicle_model',
                'chassis_number',
                'registration_number',
                'has_service_record',
                'trade_in_name',
                'trade_in_model_year',
                'trade_in_inspection_date',
                'trade_in_mileage',
                'trade_in_color',
                'trade_in_price',
                'down_payment',
                'remaining_amount',
                'credit_months',
                'credit_fee',
                'monthly_payment',
                'bonus_payment',
                'environmental_performance_tax',
                'inspection_registration_fee',
                'inspection_registration_fee_exempt',
                'trade_in_handling_fee',
                'assessment_fee',
            ]);
        });
    }
};