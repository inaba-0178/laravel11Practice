<?php

declare(strict_types=1);

namespace App\Filament\Resources\InquiryResource\Pages;

use App\Application\Services\EstimatePdfService;
use App\Application\UseCases\Estimate\CreateEstimateInputData;
use App\Application\UseCases\Estimate\CreateEstimateUseCase;
use App\Constants\InquiryStatus;
use App\Domain\Common\Repositories\DealerFeeRepositoryInterface;
use App\Domain\Common\Services\TotalPriceCalculator;
use App\Filament\Resources\InquiryResource;
use App\Infrastructure\Eloquent\User\StkEstimate;
use App\Infrastructure\Eloquent\User\StkInquiry;
use App\Infrastructure\Eloquent\Mst\MstVehicleYearVersions;
use App\Infrastructure\Eloquent\Mst\MstVehicleTax;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Page;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Application\Services\MailService;
use App\Domain\Shared\Constants\MailTemplateKey;
use App\Constants\TaxConstants;
use Illuminate\Mail\Mailables\Attachment;
use Livewire\WithFileUploads;

class ViewInquiry extends Page
{
    use WithFileUploads;

    protected static string $resource = InquiryResource::class;
    protected static string $view     = 'filament.pages.inquiry-view';

    public StkInquiry $record;

    /** 返答入力内容 */
    public string $replyText = '';

    /** 電話対応メモ */
    public string $phoneMemo = '';

    /** 見積作成フォーム表示フラグ */
    public bool $showEstimateForm = false;

    /** 見積フォームデータ */
    public array $estimateForm = [];

    /** 付属品リスト */
    public array $accessories = [];

    /** 必要書類リスト */
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

    /** 金額調整フォーム表示フラグ */
    public bool $showPriceAdjust = false;

    /** 値引き種別（tax_excluded=税抜き・tax_included=税込み） */
    public string $discountType = 'tax_excluded';

    /** 送信確認モーダル表示フラグ */
    public bool $showReplyConfirm = false;

    /** 添付ファイル（見積PDF） */
    public ?StkEstimate $attachedEstimate = null;

    /** 追加添付ファイル（ドラッグ&ドロップ） */
    public array $uploadedAttachments = [];

    /** 一時アップロードファイル */
    public $tempAttachment = null;

    public function getTitle(): string
    {
        return '問い合わせ詳細';
    }

    public function getBreadcrumb(): string
    {
        return '問い合わせ詳細';
    }

    public function mount(StkInquiry $record): void
    {
        $this->record    = $record->load(['car', 'car.series', 'car.detail', 'car.dealerFee', 'dealer']);
        $this->replyText = $record->reply      ?? '';
        $this->phoneMemo = $record->phone_memo ?? '';

        // 見積フォームの初期値を車両・問い合わせから設定
        $this->initEstimateForm();
    }

    /**
     * 見積フォームの初期値を設定
     */
    private function initEstimateForm(): void
    {
        $car         = $this->record->car;
        $calculator  = app(TotalPriceCalculator::class);
        $dealerFee   = app(DealerFeeRepositoryInterface::class)->findByCarId($car->id);
        $isLight     = $calculator->isLightVehicle($car->body_type_id);
        $vehicleType = $isLight ? 'light' : 'standard';

        $this->estimateForm = [
            'customer_name'                         => $this->record->name,
            'customer_nickname'                     => $this->record->nickname,
            'customer_phone'                        => $this->record->phone,
            'customer_postal_code'                  => $this->record->postal_code,
            'customer_address'                      => $this->record->address,
            'customer_birth_date'                   => null,
            'customer_workplace'                    => null,
            'customer_contact_phone'                => null,
            'vehicle_price'                         => (int) ($car->price ?? 0),
            'discount'                              => 0,
            'recycle_fee'                           => (int) ($car->recycle_fee ?? 0),
            'weight_tax'                            => $calculator->resolveWeightTax($car, $isLight),
            'liability_insurance'                   => $calculator->resolveLiabilityInsurance($car, $vehicleType),
            'vehicle_tax'                           => $calculator->resolveVehicleTax($car),
            'registration_fee'                      => (int) ($dealerFee?->registration_fee ?? 0),
            'garage_cert_fee'                       => (int) ($dealerFee?->garage_cert_fee ?? 0),
            'delivery_fee'                          => (int) ($dealerFee?->delivery_fee ?? 0),
            'maintenance_fee'                       => (int) ($dealerFee?->maintenance_fee ?? 0),
            'environmental_performance_tax'         => 0,
            'inspection_registration_fee'           => 0,
            'inspection_registration_fee_exempt'    => 0,
            'trade_in_handling_fee'                 => 0,
            'assessment_fee'                        => 0,
            'vehicle_model'                         => null,
            'chassis_number'                        => null,
            'registration_number'                   => null,
            'has_service_record'                    => null,
            'trade_in_name'                         => null,
            'trade_in_model_year'                   => null,
            'trade_in_inspection_date'              => null,
            'trade_in_mileage'                      => null,
            'trade_in_color'                        => null,
            'trade_in_price'                        => null,
            'down_payment'                          => null,
            'remaining_amount'                      => null,
            'credit_months'                         => null,
            'credit_fee'                            => null,
            'monthly_payment'                       => null,
            'bonus_payment'                         => null,
            'notes'                                 => '',
            'valid_until'                           => now()->addDays(30)->format('Y-m-d'),
        ];
    }

    /**
     * 諸費用を解決する
     */
    private function resolveMiscFees(object $car): array
    {
        // 重量税
        $weightTax = 0;
        if ($car->vehicle_id && $car->model_year) {
            $version = MstVehicleYearVersions::where('vehicle_id', $car->vehicle_id)
                ->where('year_from', '<=', $car->model_year)
                ->where('year_to', '>=', $car->model_year)
                ->first();

            if ($version?->weight_kg) {
                $tax = app(DealerFeeRepositoryInterface::class)
                    ->findWeightTax((int) $version->weight_kg, false);
                $weightTax = (int) ($tax?->amount ?? 0);
            }
        }

        // 自賠責保険料
        $liabilityInsurance = 0;
        $inspectionExpire   = $car->detail?->inspection_expire_date;
        if ($inspectionExpire) {
            $months     = max(0, (int) now()->diffInMonths($inspectionExpire, false));
            $insurance  = app(DealerFeeRepositoryInterface::class)
                ->findLiabilityInsurance('standard', $months);
            $liabilityInsurance = (int) ($insurance?->amount ?? 0);
        }

        // 自動車税（排気量から取得）
        $vehicleTax = 0;
        if ($car->detail?->displacement) {
            $tax = \App\Infrastructure\Eloquent\Mst\MstVehicleTax::whereHas('displacementList', function ($q) use ($car) {
                $q->where('min_amount', '<=', $car->detail->displacement)
                  ->where(function ($q2) use ($car) {
                      $q2->where('max_amount', '>=', $car->detail->displacement)
                         ->orWhere('is_unlimited', 1);
                  });
            })->where('is_light', false)->first();
            $vehicleTax = (int) ($tax?->amount ?? 0);
        }

        return [
            'weight_tax'          => $weightTax,
            'liability_insurance' => $liabilityInsurance,
            'vehicle_tax'         => $vehicleTax,
        ];
    }

    /** 付属品を追加 */
    public function addAccessory(): void
    {
        $this->accessories[] = ['name' => '', 'price' => 0];
    }

    /** 付属品を削除 */
    public function removeAccessory(int $index): void
    {
        array_splice($this->accessories, $index, 1);
    }

    /** 必要書類を追加 */
    public function addDocument(): void
    {
        $this->documents[] = ['name' => ''];
    }

    /** 必要書類を削除 */
    public function removeDocument(int $index): void
    {
        array_splice($this->documents, $index, 1);
    }

    /** 見積を作成してPDFをダウンロード */
    public function createEstimate(): void
    {
        $useCase = app(CreateEstimateUseCase::class);


        $data = new CreateEstimateInputData(
            dealerId:                           $this->record->dealer_id,
            carId:                              $this->record->car_id,
            inquiryId:                          $this->record->id,
            customerName:                       $this->estimateForm['customer_name'],
            customerNickname:                   $this->estimateForm['customer_nickname'],
            customerPhone:                      $this->estimateForm['customer_phone'],
            customerPostalCode:                 $this->estimateForm['customer_postal_code'],
            customerAddress:                    $this->estimateForm['customer_address'],
            customerBirthDate:                  $this->estimateForm['customer_birth_date'] ?? null,
            customerWorkplace:                  $this->estimateForm['customer_workplace'] ?? null,
            customerContactPhone:               $this->estimateForm['customer_contact_phone'] ?? null,
            vehiclePrice:                       (int) $this->estimateForm['vehicle_price'],
            discount:                           (int) $this->estimateForm['discount'],
            discountType:                       $this->discountType,
            recycleFee:                         (int) $this->estimateForm['recycle_fee'],
            weightTax:                          (int) $this->estimateForm['weight_tax'],
            liabilityInsurance:                 (int) $this->estimateForm['liability_insurance'],
            vehicleTax:                         (int) $this->estimateForm['vehicle_tax'],
            registrationFee:                    (int) $this->estimateForm['registration_fee'],
            garageCertFee:                      (int) $this->estimateForm['garage_cert_fee'],
            deliveryFee:                        (int) $this->estimateForm['delivery_fee'],
            maintenanceFee:                     (int) $this->estimateForm['maintenance_fee'],
            environmentalPerformanceTax:        (int) ($this->estimateForm['environmental_performance_tax'] ?? 0),
            inspectionRegistrationFee:          (int) ($this->estimateForm['inspection_registration_fee'] ?? 0),
            inspectionRegistrationFeeExempt:    (int) ($this->estimateForm['inspection_registration_fee_exempt'] ?? 0),
            tradeInHandlingFee:                 (int) ($this->estimateForm['trade_in_handling_fee'] ?? 0),
            assessmentFee:                      (int) ($this->estimateForm['assessment_fee'] ?? 0),
            vehicleModel:                       $this->estimateForm['vehicle_model'] ?? null,
            chassisNumber:                      $this->estimateForm['chassis_number'] ?? null,
            registrationNumber:                 $this->estimateForm['registration_number'] ?? null,
            hasServiceRecord:                   isset($this->estimateForm['has_service_record'])
                                                    ? (bool) $this->estimateForm['has_service_record']
                                                    : null,
            tradeInName:                        $this->estimateForm['trade_in_name'] ?? null,
            tradeInModelYear:                   $this->estimateForm['trade_in_model_year'] ?? null,
            tradeInInspectionDate:              $this->estimateForm['trade_in_inspection_date'] ?? null,
            tradeInMileage:                     isset($this->estimateForm['trade_in_mileage'])
                                                    ? (int) $this->estimateForm['trade_in_mileage']
                                                    : null,
            tradeInColor:                       $this->estimateForm['trade_in_color'] ?? null,
            tradeInPrice:                       isset($this->estimateForm['trade_in_price'])
                                                    ? (int) $this->estimateForm['trade_in_price']
                                                    : null,
            downPayment:                        isset($this->estimateForm['down_payment'])
                                                    ? (int) $this->estimateForm['down_payment']
                                                    : null,
            remainingAmount:                    isset($this->estimateForm['remaining_amount'])
                                                    ? (int) $this->estimateForm['remaining_amount']
                                                    : null,
            creditMonths:                       isset($this->estimateForm['credit_months'])
                                                    ? (int) $this->estimateForm['credit_months']
                                                    : null,
            creditFee:                          isset($this->estimateForm['credit_fee'])
                                                    ? (int) $this->estimateForm['credit_fee']
                                                    : null,
            monthlyPayment:                     isset($this->estimateForm['monthly_payment'])
                                                    ? (int) $this->estimateForm['monthly_payment']
                                                    : null,
            bonusPayment:                       isset($this->estimateForm['bonus_payment'])
                                                    ? (int) $this->estimateForm['bonus_payment']
                                                    : null,
            accessories:                        $this->accessories,
            documents:                          $this->documents,
            notes:                              $this->estimateForm['notes'] ?? null,
            validUntil:                         $this->estimateForm['valid_until'] ?? null,
            createdBy:                          Auth::id(),
        );

        $output = $useCase->execute($data);

        // 見積を添付にセット
        $this->attachedEstimate = $output->estimate;

        Notification::make()->title('見積を作成しました')->success()->send();

        // PDFダウンロード
        $this->redirect(route('estimate.download', ['estimate' => $output->estimate->id]));
    }

    /** 返信先メールアドレス */
    public function getReplyToEmail(): ?string
    {
        return $this->record->email
            ?? $this->record->member?->email
            ?? null;
    }

    /** メールアドレスがあるか */
    public function hasEmail(): bool
    {
        return $this->getReplyToEmail() !== null;
    }

    /** 一時保存 */
    public function saveDraft(): void
    {
        $this->record->update([
            'reply'  => $this->replyText,
            'status' => InquiryStatus::DRAFT,
        ]);

        Notification::make()->title('一時保存しました')->info()->send();
    }

    /** 送信（返信済みに変更） */
    public function sendReply(): void
    {
        if (empty(trim($this->replyText))) {
            Notification::make()->title('返答内容を入力してください')->danger()->send();
            return;
        }

        $this->sendReplyMail();

        $this->record->update([
            'reply'      => $this->replyText,
            'status'     => InquiryStatus::REPLIED,
            'replied_at' => now(),
            'replied_by' => Auth::id(),
        ]);

        Notification::make()->title('返信しました')->success()->send();
        $this->redirect(ListInquiries::getUrl());
    }

    /** 電話対応メモ保存 */
    public function savePhoneMemo(): void
    {
        if (empty(trim($this->phoneMemo))) {
            Notification::make()->title('電話対応メモを入力してください')->danger()->send();
            return;
        }

        $this->record->update([
            'phone_memo' => $this->phoneMemo,
            'status'     => InquiryStatus::PHONE_REPLIED,
            'replied_at' => now(),
            'replied_by' => Auth::id(),
        ]);

        Notification::make()->title('電話対応メモを保存しました')->success()->send();
        $this->redirect(ListInquiries::getUrl());
    }

    /** メール送信 */
    private function sendReplyMail(): void
    {
        $toEmail = $this->getReplyToEmail();
        if (!$toEmail) return;

        $attachments = [];

        // 見積PDF添付
        if ($this->attachedEstimate) {
            $pdfContent = app(EstimatePdfService::class)->generateContent($this->attachedEstimate);
            $attachments[] = Attachment::fromData(
                fn() => $pdfContent,
                "見積書_{$this->attachedEstimate->estimate_number}.pdf"
            )->withMime('application/pdf');
        }

        // アップロードファイル添付
        foreach ($this->uploadedAttachments as $file) {
            $attachments[] = Attachment::fromStorage($file['path'])
                ->as($file['name'])
                ->withMime($file['mime']);
        }

        try {
            app(MailService::class)->send(
                templateKey:  MailTemplateKey::INQUIRY_REPLIED,
                toEmail:      $toEmail,
                placeholders: [
                    'customer_name' => $this->getSenderName(),
                    'inquiry_type'  => $this->getInquiryTypeLabel(),
                    'reply'         => $this->replyText,
                    'dealer_name'   => $this->record->dealer?->name ?? '',
                ],
                attachments: $attachments,
            );
        } catch (\Throwable $e) {
            \Log::error('Inquiry reply mail error', ['error' => $e->getMessage()]);
        }
    }

    /** 問い合わせ種別ラベル */
    public function getInquiryTypeLabel(): string
    {
        return match($this->record->inquiry_type) {
            'stock_check'     => '在庫確認',
            'estimate'        => '見積依頼',
            'condition_check' => '車両状態確認',
            'other'           => 'その他',
            default           => $this->record->inquiry_type,
        };
    }

    /** ステータスラベル */
    public function getStatusLabel(): string
    {
        return InquiryStatus::LABELS[$this->record->status] ?? $this->record->status;
    }

    /** 問い合わせ者名 */
    public function getSenderName(): string
    {
        if ($this->record->name && $this->record->nickname) {
            return "{$this->record->name}（{$this->record->nickname}）";
        }
        return $this->record->name
            ?? $this->record->nickname
            ?? '会員';
    }

    /** 見積合計金額（プレビュー用） */
    public function getEstimateTotal(): int
    {
        $price    = (int) ($this->estimateForm['vehicle_price'] ?? 0);
        $discount = (int) ($this->estimateForm['discount'] ?? 0);

        if ($this->discountType === 'tax_excluded') {
            $discounted   = $price - $discount;
            $tax          = (int) round($discounted * TaxConstants::CONSUMPTION_TAX_RATE);
            $priceWithTax = $discounted + $tax;
        } else {
            $tax          = (int) round($price * TaxConstants::CONSUMPTION_TAX_RATE);
            $priceWithTax = $price + $tax - $discount;
        }

        $misc = (int) ($this->estimateForm['recycle_fee'] ?? 0)
            + (int) ($this->estimateForm['weight_tax'] ?? 0)
            + (int) ($this->estimateForm['liability_insurance'] ?? 0)
            + (int) ($this->estimateForm['vehicle_tax'] ?? 0)
            + (int) ($this->estimateForm['registration_fee'] ?? 0)
            + (int) ($this->estimateForm['garage_cert_fee'] ?? 0)
            + (int) ($this->estimateForm['delivery_fee'] ?? 0)
            + (int) ($this->estimateForm['maintenance_fee'] ?? 0);

        $accessoriesTotal = collect($this->accessories)->sum(fn($a) => (int)($a['price'] ?? 0));

        return $priceWithTax + $misc + $accessoriesTotal;
    }

    /** 見積添付を外す */
    public function removeEstimateAttachment(): void
    {
        $this->attachedEstimate = null;
    }

    /** ファイルがアップロードされたら一時保存 */
    public function updatedTempAttachment(): void
    {
        if ($this->tempAttachment) {
            // 一時ディレクトリに保存してパスを記録
            $path = $this->tempAttachment->store('temp-attachments', 'local');
            $this->uploadedAttachments[] = [
                'path' => $path,
                'name' => $this->tempAttachment->getClientOriginalName(),
                'mime' => $this->tempAttachment->getMimeType(),
            ];
            $this->tempAttachment = null;
        }
    }

    /** アップロードファイルを削除 */
    public function removeUploadedAttachment(int $index): void
    {
        // 一時ファイルも削除
        if (isset($this->uploadedAttachments[$index])) {
            \Storage::disk('local')->delete($this->uploadedAttachments[$index]['path']);
        }
        array_splice($this->uploadedAttachments, $index, 1);
    }

    /** 送信確認モーダルを開く */
    public function openReplyConfirm(): void
    {
        if (empty(trim($this->replyText))) {
            Notification::make()->title('返答内容を入力してください')->danger()->send();
            return;
        }
        $this->showReplyConfirm = true;
    }

    /** 送信確認モーダルから送信 */
    public function confirmAndSendReply(): void
    {
        $this->sendReplyMail();

        $this->record->update([
            'reply'      => $this->replyText,
            'status'     => InquiryStatus::REPLIED,
            'replied_at' => now(),
            'replied_by' => Auth::id(),
        ]);

        $this->showReplyConfirm = false;
        Notification::make()->title('返信しました')->success()->send();
        $this->redirect(ListInquiries::getUrl());
    }
}