<?php

declare(strict_types=1);

namespace App\Application\UseCases\Estimate;

final class CreateEstimateInputData
{
    public function __construct(
        public readonly int     $dealerId,
        public readonly int     $carId,
        public readonly ?int    $inquiryId,

        // 顧客情報
        public readonly ?string $customerName,
        public readonly ?string $customerNickname,
        public readonly ?string $customerPhone,
        public readonly ?string $customerPostalCode,
        public readonly ?string $customerAddress,
        public readonly ?string $customerBirthDate,
        public readonly ?string $customerWorkplace,
        public readonly ?string $customerContactPhone,

        // 価格情報
        public readonly int     $vehiclePrice,
        public readonly int     $discount,
        public readonly string  $discountType,
        public readonly int     $recycleFee,
        public readonly int     $weightTax,
        public readonly int     $liabilityInsurance,
        public readonly int     $vehicleTax,
        public readonly int     $registrationFee,
        public readonly int     $garageCertFee,
        public readonly int     $deliveryFee,
        public readonly int     $maintenanceFee,
        public readonly int     $environmentalPerformanceTax,
        public readonly int     $inspectionRegistrationFee,  
        public readonly int     $inspectionRegistrationFeeExempt,
        public readonly int     $tradeInHandlingFee, 
        public readonly int     $assessmentFee,      

        // 車両情報
        public readonly ?string $vehicleModel,
        public readonly ?string $chassisNumber, 
        public readonly ?string $registrationNumber,  
        public readonly ?bool   $hasServiceRecord, 

        // 下取車情報
        public readonly ?string $tradeInName, 
        public readonly ?string $tradeInModelYear, 
        public readonly ?string $tradeInInspectionDate,
        public readonly ?int    $tradeInMileage,
        public readonly ?string $tradeInColor,
        public readonly ?int    $tradeInPrice,

        // 支払い情報
        public readonly ?int    $downPayment, 
        public readonly ?int    $remainingAmount,
        public readonly ?int    $creditMonths,
        public readonly ?int    $creditFee,   
        public readonly ?int    $monthlyPayment,
        public readonly ?int    $bonusPayment,

        // その他
        public readonly array   $accessories,
        public readonly array   $documents,
        public readonly ?string $notes,
        public readonly ?string $validUntil,
        public readonly int     $createdBy,
    ) {}
}