<?php

declare(strict_types=1);

namespace App\Presentation\Controllers\CarDocument;

use App\Application\Services\Pdf\ContractPdfService;
use App\Application\Services\Pdf\EstimatePdfService;
use App\Domain\Shared\Enums\PdfDocumentType;
use App\Infrastructure\Eloquent\User\StkCar;
use App\Infrastructure\Eloquent\User\StkEstimate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CarDocumentDownloadController
{
    public function __construct(
        private readonly EstimatePdfService $estimatePdfService,
        private readonly ContractPdfService $contractPdfService,
    ) {}

    public function __invoke(Request $request)
    {
        $data = session()->pull('car_document_data');

        if (!$data) {
            abort(404);
        }

        $car      = StkCar::with(['series', 'vehicle', 'manufacturer', 'detail', 'dealer', 'dealerFee'])->findOrFail($data['car_id']);
        $estimate = $this->buildEstimate($data, $car);

        if ($data['documentType'] === PdfDocumentType::CONTRACT) {
            return $this->contractPdfService->download($estimate);
        }

        return $this->estimatePdfService->download($estimate);
    }

    private function buildEstimate(array $data, StkCar $car): StkEstimate
    {
        $estimate = new StkEstimate();

        $estimate->car_id                             = $car->id;
        $estimate->dealer_id                          = $car->dealer_id;
        $estimate->created_by                         = Auth::id();
        $estimate->estimate_number                    = $this->generateDocumentNumber($data['documentType']);
        $estimate->customer_name                      = $data['customerName'];
        $estimate->customer_nickname                  = $data['customerNickname'];
        $estimate->customer_phone                     = $data['customerPhone'];
        $estimate->customer_postal_code               = $data['customerPostalCode'];
        $estimate->customer_address                   = $data['customerAddress'];
        $estimate->customer_birth_date                = $data['customerBirthDate'];
        $estimate->customer_workplace                 = $data['customerWorkplace'];
        $estimate->customer_contact_phone             = $data['customerContactPhone'];
        $estimate->vehicle_model                      = $data['vehicleModel'];
        $estimate->chassis_number                     = $data['chassisNumber'];
        $estimate->registration_number                = $data['registrationNumber'];
        $estimate->has_service_record                 = $data['hasServiceRecord'];
        $estimate->vehicle_price                      = $data['vehiclePrice'];
        $estimate->discount                           = $data['discount'];
        $estimate->discount_type                      = $data['discountType'];
        $estimate->recycle_fee                        = $data['recycleFee'];
        $estimate->vehicle_tax                        = $data['vehicleTax'];
        $estimate->weight_tax                         = $data['weightTax'];
        $estimate->liability_insurance                = $data['liabilityInsurance'];
        $estimate->registration_fee                   = $data['registrationFee'];
        $estimate->garage_cert_fee                    = $data['garageCertFee'];
        $estimate->delivery_fee                       = $data['deliveryFee'];
        $estimate->maintenance_fee                    = $data['maintenanceFee'];
        $estimate->environmental_performance_tax      = $data['environmentalPerformanceTax'];
        $estimate->inspection_registration_fee        = $data['inspectionRegistrationFee'];
        $estimate->inspection_registration_fee_exempt = $data['inspectionRegistrationFeeExempt'];
        $estimate->trade_in_handling_fee              = $data['tradeInHandlingFee'];
        $estimate->assessment_fee                     = $data['assessmentFee'];
        $estimate->trade_in_name                      = $data['tradeInName'];
        $estimate->trade_in_model_year                = $data['tradeInModelYear'];
        $estimate->trade_in_inspection_date           = $data['tradeInInspectionDate'];
        $estimate->trade_in_mileage                   = $data['tradeInMileage'];
        $estimate->trade_in_color                     = $data['tradeInColor'];
        $estimate->trade_in_price                     = $data['tradeInPrice'];
        $estimate->down_payment                       = $data['downPayment'];
        $estimate->remaining_amount                   = $data['remainingAmount'];
        $estimate->credit_months                      = $data['creditMonths'];
        $estimate->credit_fee                         = $data['creditFee'];
        $estimate->monthly_payment                    = $data['monthlyPayment'];
        $estimate->bonus_payment                      = $data['bonusPayment'];
        $estimate->accessories                        = $data['accessories'];
        $estimate->documents                          = $data['documents'];
        $estimate->notes                              = $data['notes'];
        $estimate->valid_until                        = $data['validUntil'];
        $estimate->created_at                         = now();

        $estimate->setRelation('car', $car);
        $estimate->setRelation('dealer', $car->dealer);
        $estimate->setRelation('createdBy', Auth::user());

        return $estimate;
    }

    private function generateDocumentNumber(string $documentType): string
    {
        $prefix = $documentType === PdfDocumentType::CONTRACT ? 'ORD' : 'EST';
        $date   = now()->format('Ymd');
        $seq    = str_pad(
            (string)(StkEstimate::whereDate('created_at', today())->count() + 1),
            4, '0', STR_PAD_LEFT
        );

        return "{$prefix}-{$date}-{$seq}";
    }
}