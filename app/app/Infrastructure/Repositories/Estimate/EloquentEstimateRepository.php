<?php

declare(strict_types=1);

namespace App\Infrastructure\Repositories\Estimate;

use App\Application\UseCases\Estimate\CreateEstimateInputData;
use App\Infrastructure\Eloquent\User\StkEstimate;

/**
 * 見積リポジトリ（Eloquent実装）
 */
class EloquentEstimateRepository
{
    /**
     * 見積をDBに保存する
     */
    public function create(CreateEstimateInputData $data, string $estimateNumber): StkEstimate
    {
        return StkEstimate::create([
            'estimate_number'                    => $estimateNumber,
            'dealer_id'                          => $data->dealerId,
            'car_id'                             => $data->carId,
            'inquiry_id'                         => $data->inquiryId,

            // 顧客情報
            'customer_name'                      => $data->customerName,
            'customer_nickname'                  => $data->customerNickname,
            'customer_phone'                     => $data->customerPhone,
            'customer_postal_code'               => $data->customerPostalCode,
            'customer_address'                   => $data->customerAddress,
            'customer_birth_date'                => $data->customerBirthDate,
            'customer_workplace'                 => $data->customerWorkplace,
            'customer_contact_phone'             => $data->customerContactPhone,

            // 価格情報
            'vehicle_price'                      => $data->vehiclePrice,
            'discount'                           => $data->discount,
            'discount_type'                      => $data->discountType,
            'recycle_fee'                        => $data->recycleFee,
            'weight_tax'                         => $data->weightTax,
            'liability_insurance'                => $data->liabilityInsurance,
            'vehicle_tax'                        => $data->vehicleTax,
            'registration_fee'                   => $data->registrationFee,
            'garage_cert_fee'                    => $data->garageCertFee,
            'delivery_fee'                       => $data->deliveryFee,
            'maintenance_fee'                    => $data->maintenanceFee,
            'environmental_performance_tax'      => $data->environmentalPerformanceTax,
            'inspection_registration_fee'        => $data->inspectionRegistrationFee,
            'inspection_registration_fee_exempt' => $data->inspectionRegistrationFeeExempt,
            'trade_in_handling_fee'              => $data->tradeInHandlingFee,
            'assessment_fee'                     => $data->assessmentFee,

            // 車両情報
            'vehicle_model'                      => $data->vehicleModel,
            'chassis_number'                     => $data->chassisNumber,
            'registration_number'                => $data->registrationNumber,
            'has_service_record'                 => $data->hasServiceRecord,

            // 下取車情報
            'trade_in_name'                      => $data->tradeInName,
            'trade_in_model_year'                => $data->tradeInModelYear,
            'trade_in_inspection_date'           => $data->tradeInInspectionDate,
            'trade_in_mileage'                   => $data->tradeInMileage,
            'trade_in_color'                     => $data->tradeInColor,
            'trade_in_price'                     => $data->tradeInPrice,

            // 支払い情報
            'down_payment'                       => $data->downPayment,
            'remaining_amount'                   => $data->remainingAmount,
            'credit_months'                      => $data->creditMonths,
            'credit_fee'                         => $data->creditFee,
            'monthly_payment'                    => $data->monthlyPayment,
            'bonus_payment'                      => $data->bonusPayment,

            // その他
            'accessories'                        => $data->accessories,
            'documents'                          => $data->documents,
            'notes'                              => $data->notes,
            'valid_until'                        => $data->validUntil,
            'created_by'                         => $data->createdBy,
        ]);
    }
}