<?php

declare(strict_types=1);

namespace App\Filament\Resources\CarStockResource\Pages;

use App\Application\Services\Pdf\EstimatePdfService;
use App\Application\Services\Pdf\ContractPdfService;
use App\Application\UseCases\Estimate\CreateEstimateInputData;
use App\Application\UseCases\Estimate\CreateEstimateUseCase;
use App\Domain\Shared\Enums\PdfDocumentType;
use App\Filament\Resources\CarStockResource;
use App\Infrastructure\Eloquent\User\StkCar;
use App\Infrastructure\Eloquent\User\StkEstimate;
use App\Domain\Common\Services\TotalPriceCalculator;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Page;
use Illuminate\Support\Facades\Auth;

class CreateCarDocument extends Page
{
    protected static string $resource = CarStockResource::class;
    protected static string $view     = 'filament.pages.create-car-document';

    public StkCar $record;

    /** ドキュメント種別 estimate or contract */
    public string $documentType = PdfDocumentType::ESTIMATE;

    /** 顧客情報 */
    public string  $customerName          = '';
    public ?string $customerNickname      = null;
    public ?string $customerPhone         = null;
    public ?string $customerPostalCode    = null;
    public ?string $customerAddress       = null;
    public ?string $customerBirthDate     = null;
    public ?string $customerWorkplace     = null;
    public ?string $customerContactPhone  = null;

    /** 車両情報 */
    public ?string $vehicleModel        = null;
    public ?string $chassisNumber       = null;
    public ?string $registrationNumber  = null;
    public ?string $hasServiceRecord    = null;

    /** 価格情報 */
    public int     $vehiclePrice            = 0;
    public int     $discount                = 0;
    public string  $discountType            = 'tax_excluded';
    public int     $recycleFee              = 0;
    public int     $vehicleTax              = 0;
    public int     $weightTax               = 0;
    public int     $liabilityInsurance      = 0;
    public int     $registrationFee         = 0;
    public int     $garageCertFee           = 0;
    public int     $deliveryFee             = 0;
    public int     $maintenanceFee          = 0;
    public int     $environmentalPerformanceTax      = 0;
    public int     $inspectionRegistrationFee        = 0;
    public int     $inspectionRegistrationFeeExempt  = 0;
    public int     $tradeInHandlingFee      = 0;
    public int     $assessmentFee           = 0;

    /** 下取車情報 */
    public ?string $tradeInName             = null;
    public ?string $tradeInModelYear        = null;
    public ?string $tradeInInspectionDate   = null;
    public ?int    $tradeInMileage          = null;
    public ?string $tradeInColor            = null;
    public ?int    $tradeInPrice            = null;

    /** 支払い情報 */
    public ?int    $downPayment      = null;
    public ?int    $remainingAmount  = null;
    public ?int    $creditMonths     = null;
    public ?int    $creditFee        = null;
    public ?int    $monthlyPayment   = null;
    public ?int    $bonusPayment     = null;

    /** 付属品 */
    public array $accessories = [];

    /** 必要書類 */
    public array $documents = [
        ['name' => '印鑑証明'],
        ['name' => '住民票'],
        ['name' => '軽自動車住所証明'],
        ['name' => '納税証明（下取車）'],
        ['name' => '自認書・承諾書'],
        ['name' => '委任状'],
        ['name' => '譲渡証明'],
        ['name' => '保証人印鑑証明'],
    ];

    /** 備考 */
    public ?string $notes       = null;
    public ?string $validUntil  = null;

    /** 金額調整表示フラグ */
    public bool $showPriceAdjust = false;

    public function mount(StkCar $record): void
    {
        $this->record = $record->load([
            'series', 'vehicle', 'manufacturer',
            'detail', 'dealer', 'dealerFee',
        ]);

        $this->initForm();
    }

    public function getTitle(): string
    {
        return '見積書／契約書を作成 - ' . ($this->record->series?->series_name ?? '');
    }

    public function getBackUrl(): string
    {
        return CarStockResource::getUrl('view', ['record' => $this->record->id]);
    }

    /**
     * フォームの初期値をセット
     */
    private function initForm(): void
    {
        $calculator = app(TotalPriceCalculator::class);

        $car        = $this->record;
        $isLight    = $calculator->isLightVehicle($car->body_type_id);
        $vehicleType = $isLight ? 'light' : 'standard';

        $fees = $calculator->calculate($car);

        $this->vehiclePrice       = (int) $car->price;
        $this->recycleFee         = (int) ($car->recycle_fee ?? 0);
        $this->vehicleTax         = $calculator->resolveVehicleTax($car);
        $this->weightTax          = $calculator->resolveWeightTax($car, $isLight);
        $this->liabilityInsurance = $calculator->resolveLiabilityInsurance($car, $vehicleType);
        $this->registrationFee    = (int) ($fees->registrationFee ?? 0);
        $this->garageCertFee      = (int) ($fees->garageCertFee ?? 0);
        $this->deliveryFee        = (int) ($fees->deliveryFee ?? 0);
        $this->maintenanceFee     = (int) ($fees->maintenanceFee ?? 0);
        $this->validUntil         = now()->addMonth()->format('Y-m-d');
    }

    /**
     * 見積合計を取得（プレビュー用）
     */
    public function getEstimateTotal(): int
    {
        $discountedPrice = $this->discountType === 'tax_excluded'
            ? $this->vehiclePrice - $this->discount
            : $this->vehiclePrice;

        $consumptionTax = $this->discountType === 'tax_excluded'
            ? (int) round($discountedPrice * \App\Constants\TaxConstants::CONSUMPTION_TAX_RATE)
            : (int) round($this->vehiclePrice * \App\Constants\TaxConstants::CONSUMPTION_TAX_RATE) - $this->discount;

        $accessoriesTotal = collect($this->accessories)->sum(fn($a) => (int)($a['price'] ?? 0));

        $miscTotal = $this->vehicleTax
            + $this->weightTax
            + $this->liabilityInsurance
            + $this->recycleFee
            + $this->registrationFee
            + $this->garageCertFee
            + $this->deliveryFee
            + $this->maintenanceFee
            + $this->environmentalPerformanceTax
            + $this->inspectionRegistrationFee
            + $this->inspectionRegistrationFeeExempt
            + $this->tradeInHandlingFee
            + $this->assessmentFee;

        return $discountedPrice + $consumptionTax + $accessoriesTotal + $miscTotal;
    }

    /** 付属品追加 */
    public function addAccessory(): void
    {
        $this->accessories[] = ['name' => '', 'price' => 0];
    }

    /** 付属品削除 */
    public function removeAccessory(int $index): void
    {
        array_splice($this->accessories, $index, 1);
    }

    /** 書類追加 */
    public function addDocument(): void
    {
        $this->documents[] = ['name' => ''];
    }

    /** 書類削除 */
    public function removeDocument(int $index): void
    {
        array_splice($this->documents, $index, 1);
    }

    /**
     * PDF生成・ダウンロード
     */
    public function createDocument(): void
    {
        // セッションにフォームデータを保存してダウンロード用ルートにリダイレクト
        session()->put('car_document_data', [
            'documentType'                      => $this->documentType,
            'car_id'                            => $this->record->id,
            'customerName'                      => $this->customerName,
            'customerNickname'                  => $this->customerNickname,
            'customerPhone'                     => $this->customerPhone,
            'customerPostalCode'                => $this->customerPostalCode,
            'customerAddress'                   => $this->customerAddress,
            'customerBirthDate'                 => $this->customerBirthDate,
            'customerWorkplace'                 => $this->customerWorkplace,
            'customerContactPhone'              => $this->customerContactPhone,
            'vehicleModel'                      => $this->vehicleModel,
            'chassisNumber'                     => $this->chassisNumber,
            'registrationNumber'                => $this->registrationNumber,
            'hasServiceRecord'                  => $this->hasServiceRecord,
            'vehiclePrice'                      => $this->vehiclePrice,
            'discount'                          => $this->discount,
            'discountType'                      => $this->discountType,
            'recycleFee'                        => $this->recycleFee,
            'vehicleTax'                        => $this->vehicleTax,
            'weightTax'                         => $this->weightTax,
            'liabilityInsurance'                => $this->liabilityInsurance,
            'registrationFee'                   => $this->registrationFee,
            'garageCertFee'                     => $this->garageCertFee,
            'deliveryFee'                       => $this->deliveryFee,
            'maintenanceFee'                    => $this->maintenanceFee,
            'environmentalPerformanceTax'       => $this->environmentalPerformanceTax,
            'inspectionRegistrationFee'         => $this->inspectionRegistrationFee,
            'inspectionRegistrationFeeExempt'   => $this->inspectionRegistrationFeeExempt,
            'tradeInHandlingFee'                => $this->tradeInHandlingFee,
            'assessmentFee'                     => $this->assessmentFee,
            'tradeInName'                       => $this->tradeInName,
            'tradeInModelYear'                  => $this->tradeInModelYear,
            'tradeInInspectionDate'             => $this->tradeInInspectionDate,
            'tradeInMileage'                    => $this->tradeInMileage,
            'tradeInColor'                      => $this->tradeInColor,
            'tradeInPrice'                      => $this->tradeInPrice,
            'downPayment'                       => $this->downPayment,
            'remainingAmount'                   => $this->remainingAmount,
            'creditMonths'                      => $this->creditMonths,
            'creditFee'                         => $this->creditFee,
            'monthlyPayment'                    => $this->monthlyPayment,
            'bonusPayment'                      => $this->bonusPayment,
            'accessories'                       => $this->accessories,
            'documents'                         => $this->documents,
            'notes'                             => $this->notes,
            'validUntil'                        => $this->validUntil,
        ]);

        $this->redirect(route('car-document.download'));
    }

    /**
     * StkEstimateを一時的に生成する（DBに保存しない）
     */
    private function buildEstimate(): StkEstimate
    {
        $estimate = new StkEstimate();

        $estimate->car_id                           = $this->record->id;
        $estimate->dealer_id                        = $this->record->dealer_id;
        $estimate->created_by                       = Auth::id();
        $estimate->estimate_number                  = $this->generateDocumentNumber();
        $estimate->customer_name                    = $this->customerName;
        $estimate->customer_nickname                = $this->customerNickname;
        $estimate->customer_phone                   = $this->customerPhone;
        $estimate->customer_postal_code             = $this->customerPostalCode;
        $estimate->customer_address                 = $this->customerAddress;
        $estimate->customer_birth_date              = $this->customerBirthDate;
        $estimate->customer_workplace               = $this->customerWorkplace;
        $estimate->customer_contact_phone           = $this->customerContactPhone;
        $estimate->vehicle_model                    = $this->vehicleModel;
        $estimate->chassis_number                   = $this->chassisNumber;
        $estimate->registration_number              = $this->registrationNumber;
        $estimate->has_service_record               = $this->hasServiceRecord;
        $estimate->vehicle_price                    = $this->vehiclePrice;
        $estimate->discount                         = $this->discount;
        $estimate->discount_type                    = $this->discountType;
        $estimate->recycle_fee                      = $this->recycleFee;
        $estimate->vehicle_tax                      = $this->vehicleTax;
        $estimate->weight_tax                       = $this->weightTax;
        $estimate->liability_insurance              = $this->liabilityInsurance;
        $estimate->registration_fee                 = $this->registrationFee;
        $estimate->garage_cert_fee                  = $this->garageCertFee;
        $estimate->delivery_fee                     = $this->deliveryFee;
        $estimate->maintenance_fee                  = $this->maintenanceFee;
        $estimate->environmental_performance_tax    = $this->environmentalPerformanceTax;
        $estimate->inspection_registration_fee      = $this->inspectionRegistrationFee;
        $estimate->inspection_registration_fee_exempt = $this->inspectionRegistrationFeeExempt;
        $estimate->trade_in_handling_fee            = $this->tradeInHandlingFee;
        $estimate->assessment_fee                   = $this->assessmentFee;
        $estimate->trade_in_name                    = $this->tradeInName;
        $estimate->trade_in_model_year              = $this->tradeInModelYear;
        $estimate->trade_in_inspection_date         = $this->tradeInInspectionDate;
        $estimate->trade_in_mileage                 = $this->tradeInMileage;
        $estimate->trade_in_color                   = $this->tradeInColor;
        $estimate->trade_in_price                   = $this->tradeInPrice;
        $estimate->down_payment                     = $this->downPayment;
        $estimate->remaining_amount                 = $this->remainingAmount;
        $estimate->credit_months                    = $this->creditMonths;
        $estimate->credit_fee                       = $this->creditFee;
        $estimate->monthly_payment                  = $this->monthlyPayment;
        $estimate->bonus_payment                    = $this->bonusPayment;
        $estimate->accessories                      = $this->accessories;
        $estimate->documents                        = $this->documents;
        $estimate->notes                            = $this->notes;
        $estimate->valid_until                      = $this->validUntil;
        $estimate->created_at                       = now();

        // リレーションをセット
        $estimate->setRelation('car', $this->record);
        $estimate->setRelation('dealer', $this->record->dealer);
        $estimate->setRelation('createdBy', Auth::user());

        return $estimate;
    }

    /**
     * ドキュメント番号を生成する
     */
    private function generateDocumentNumber(): string
    {
        $prefix = $this->documentType === PdfDocumentType::CONTRACT ? 'ORD' : 'EST';
        $date   = now()->format('Ymd');
        $seq    = str_pad((string)(StkEstimate::whereDate('created_at', today())->count() + 1), 4, '0', STR_PAD_LEFT);

        return "{$prefix}-{$date}-{$seq}";
    }
}