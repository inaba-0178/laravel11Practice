<?php

declare(strict_types=1);

namespace App\Filament\Resources\CarApprovalResource\Pages;

use App\Application\Services\MailService;
use App\Constants\CarStatus;
use App\Domain\Shared\Constants\MailTemplateKey;
use App\Filament\Resources\CarApprovalResource;
use App\Infrastructure\Eloquent\Mst\MstCarSeries;
use App\Infrastructure\Eloquent\Mst\MstVehicles;
use App\Infrastructure\Eloquent\Mst\MstEquipmentSafety;
use App\Infrastructure\Eloquent\Mst\MstEquipmentBasic;
use App\Infrastructure\Eloquent\Mst\MstSeatOption;
use App\Infrastructure\Eloquent\Mst\MstEquipmentDressup;
use App\Infrastructure\Eloquent\Mst\MstEquipmentEnv;
use App\Infrastructure\Eloquent\User\StkCar;
use App\Infrastructure\Eloquent\User\StkCarImages;
use App\Models\User;
use App\Notifications\CarRegistrationStatusNotification;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Page;
use Illuminate\Support\Facades\Storage;
use App\Constants\CarOptionCategory;
use App\Constants\SpecialTypeOption;
use App\Constants\SalesOption;
use App\Constants\AudioOption;
use App\Constants\NaviOption;
use App\Domain\Shared\Enums\RepairHistory;
use App\Constants\SteeringWheel;
use App\Infrastructure\Eloquent\User\StkCarLoan;
use App\Constants\LoanPlanLabel;

class ViewCarApproval extends Page
{
    protected static string $resource = CarApprovalResource::class;
    protected static string $view     = 'filament.pages.car-approval-view';

    public StkCar $record;

    // 差し戻し入力
    public string $generalComment = '';
    public array  $flaggedImages  = [];
    public array  $rejectionItems = [];

    public function getTitle(): string
    {
        return '車両承認';
    }

    public function getBreadcrumb(): string
    {
        return '車両承認';
    }

    public function mount(StkCar $record): void
    {
        $this->record = $record;

        if ($record->rejection_reason && is_array($record->rejection_reason)) {
            $this->generalComment = $record->rejection_reason['general_comment'] ?? '';
            $this->flaggedImages  = $record->rejection_reason['flagged_images'] ?? [];
            $this->rejectionItems = $record->rejection_reason['items'] ?? [];
        }
    }

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\Action::make('back')
                ->label('一覧に戻る')
                ->color('gray')
                ->url(ListCarApprovals::getUrl()),
        ];
    }

    // ===== 画像指摘トグル =====
    public function toggleImageFlag(int $imageId): void
    {
        $existing = collect($this->flaggedImages)->firstWhere('id', $imageId);
        if ($existing) {
            $this->flaggedImages = collect($this->flaggedImages)
                ->filter(fn ($img) => $img['id'] !== $imageId)
                ->values()->toArray();
        } else {
            $this->flaggedImages[] = [
                'id'              => $imageId,
                'reason'          => '',
                'dealer_response' => '',
                'resolved'        => false,
            ];
        }
    }

    public function updateImageReason(int $imageId, string $reason): void
    {
        $this->flaggedImages = collect($this->flaggedImages)
            ->map(fn ($img) => $img['id'] === $imageId ? array_merge($img, ['reason' => $reason]) : $img)
            ->toArray();
    }

    // ===== 項目指摘 =====
    public function addRejectionItem(): void
    {
        $this->rejectionItems[] = [
            'category'        => '',
            'reason'          => '',
            'dealer_response' => '',
            'resolved'        => false,
        ];
    }

    public function removeRejectionItem(int $index): void
    {
        array_splice($this->rejectionItems, $index, 1);
    }

    public function updateRejectionItem(int $index, string $field, string $value): void
    {
        if (isset($this->rejectionItems[$index])) {
            $this->rejectionItems[$index][$field] = $value;
        }
    }

    // ===== 項目を完了にする =====
    public function resolveItem(int $index): void
    {
        if (isset($this->rejectionItems[$index])) {
            $this->rejectionItems[$index]['resolved'] = true;
            $this->saveRejectionReason();
            Notification::make()->title('項目を完了にしました')->success()->send();
        }
    }

    public function resolveImage(int $imageId): void
    {
        $this->flaggedImages = collect($this->flaggedImages)
            ->map(fn ($img) => $img['id'] === $imageId ? array_merge($img, ['resolved' => true]) : $img)
            ->toArray();
        $this->saveRejectionReason();
        Notification::make()->title('画像指摘を完了にしました')->success()->send();
    }

    private function saveRejectionReason(): void
    {
        $this->record->update([
            'rejection_reason' => [
                'general_comment' => $this->generalComment,
                'flagged_images'  => $this->flaggedImages,
                'items'           => $this->rejectionItems,
            ],
        ]);
    }

    // ===== 承認 =====
    public function approve(): void
    {
        $this->record->update([
            'status'           => CarStatus::AVAILABLE,
            'rejection_reason' => null,
            'published_at'     => now(),
        ]);

        $this->sendDealerNotification(
            templateKey:  MailTemplateKey::CAR_REGISTRATION_APPROVED_DEALER,
            placeholders: [
                'dealer_name'   => $this->record->dealer?->name ?? '',
                'car_name'      => $this->record->series?->series_name ?? '',
                'registered_at' => $this->record->created_at?->format('Y/m/d H:i'),
                'approved_at'   => now()->format('Y/m/d H:i'),
                'admin_comment' => '',
            ],
            status:  'approved',
            message: '車両が承認されました。',
        );

        Notification::make()->title('承認しました')->success()->send();
        $this->redirect(ListCarApprovals::getUrl());
    }

    // ===== 差し戻し =====
    public function reject(): void
    {
        if (empty(trim($this->generalComment)) && empty($this->flaggedImages) && empty($this->rejectionItems)) {
            Notification::make()->title('指摘内容を入力してください')->danger()->send();
            return;
        }

        $rejectionReason = [
            'general_comment' => $this->generalComment,
            'flagged_images'  => $this->flaggedImages,
            'items'           => $this->rejectionItems,
        ];

        $this->record->update([
            'status'           => CarStatus::REJECTED,
            'rejection_reason' => $rejectionReason,
        ]);

        $itemText = collect($this->rejectionItems)
            ->map(fn ($item) => "[{$item['category']}] {$item['reason']}")
            ->join("\n");

        $this->sendDealerNotification(
            templateKey:  MailTemplateKey::CAR_REGISTRATION_REJECTED_DEALER,
            placeholders: [
                'dealer_name'   => $this->record->dealer?->name ?? '',
                'car_name'      => $this->record->series?->series_name ?? '',
                'registered_at' => $this->record->created_at?->format('Y/m/d H:i'),
                'rejected_at'   => now()->format('Y/m/d H:i'),
                'reject_reason' => $this->generalComment . "\n" . $itemText,
            ],
            status:  'rejected',
            message: '車両が差し戻されました。指摘内容をご確認ください。',
        );

        Notification::make()->title('差し戻しました')->danger()->send();
        $this->redirect(ListCarApprovals::getUrl());
    }

    private function sendDealerNotification(string $templateKey, array $placeholders, string $status, string $message): void
    {
        $dealerEmail = $this->record->dealer?->email;
        if ($dealerEmail) {
            try {
                app(MailService::class)->send(templateKey: $templateKey, toEmail: $dealerEmail, placeholders: $placeholders);
            } catch (\Throwable $e) {
                \Log::error('CarApproval mail error', ['error' => $e->getMessage()]);
            }
        }

        User::where('dealer_id', $this->record->dealer_id)
            ->where('is_active', 1)
            ->get()
            ->each(fn (User $u) => $u->notify(
                new CarRegistrationStatusNotification(
                    carName: $this->record->series?->series_name ?? '不明',
                    status:  $status,
                    message: $message,
                    carId:   $this->record->id,
                )
            ));
    }

    public function getCarData(): array
    {
        $car     = $this->record->load(['series', 'detail', 'options', 'dealer', 'dealerFee']);
        $series  = MstCarSeries::find($car->series_id);
        $vehicle = MstVehicles::find($car->vehicle_id);
        
        return [
            'car'               => $car,
            'series'            => $series,
            'vehicle'           => $vehicle,
            'status_label'      => CarStatus::LABELS[$car->status] ?? $car->status,
            'repair_label'      => RepairHistory::LABELS[$car->repair_history] ?? '-',
            'steering_label'    => SteeringWheel::LABELS[$car->detail?->steering_wheel ?? ''] ?? '-',
        ];
    }

    public function getImages(): array
    {
        return StkCarImages::where('car_id', $this->carId ?? $this->record->id)
            ->orderBy('display_order')
            ->get()
            ->map(fn ($img) => [
                'id'      => $img->id,
                'url'     => Storage::disk('s3')->url($img->image_url),
                'type'    => $img->image_type,
                'is_main' => $img->is_main,
            ])
            ->toArray();
    }

    public function isFlagged(int $imageId): bool
    {
        return collect($this->flaggedImages)->contains('id', $imageId);
    }

    public function getFlaggedData(int $imageId): array
    {
        return collect($this->flaggedImages)->firstWhere('id', $imageId) ?? [];
    }

    public function getEquipmentByCategory(): array
    {
        $equipped = collect($this->record->options)
            ->where('is_equipped', 1)
            ->pluck('option_name')
            ->toArray();

        $categories = [
            'safety'        => ['label' => '安全装備',    'class' => MstEquipmentSafety::class,  'items' => []],
            'basic'         => ['label' => '快適装備',    'class' => MstEquipmentBasic::class,   'items' => []],
            'seat'          => ['label' => 'インテリア',  'class' => MstSeatOption::class,        'items' => []],
            'dress_up'      => ['label' => 'エクステリア','class' => MstEquipmentDressup::class,  'items' => []],
            'environmental' => ['label' => '環境装備',    'class' => MstEquipmentEnv::class,      'items' => []],
        ];

        foreach ($categories as $key => &$cat) {
            $cat['items'] = $cat['class']::where('is_active', 1)
                ->orderBy('sort_order')
                ->get()
                ->map(fn ($opt) => [
                    'value'       => $opt->value,
                    'label'       => $opt->label,
                    'is_equipped' => in_array($opt->value, $equipped),
                ])
                ->toArray();
            unset($cat['class']);
        }

        return $categories;
    }

    public function getOtherOptions(): array
    {
        $options = $this->record->options
            ->where('option_category', 'other')  // otherのみに絞る
            ->where('is_equipped', 1)
            ->whereNotIn('option_name', SalesOption::SALES_OPTIONS)
            ->groupBy('option_category');

        $result = [];
        if ($options->has('other') && $options['other']->count() > 0) {
            $result['other'] = [
                'label' => 'その他',
                'items' => $options['other']->pluck('option_name')->toArray(),
            ];
        }

        return $result;
    }

    public function getLoans(): array
    {
        if ($this->record->loans->isEmpty()) {
            return [];
        }
    
        return $this->record->loans->map(function ($loan) {
            $rate           = $loan->getEffectiveRate();
            $monthsOptions  = $loan->getEffectiveMonthsOptions();
            $minMonths      = min($monthsOptions);
            $monthlyPayment = $loan->calcMonthlyPayment((int) $this->record->price, $minMonths);
    
            // システムデフォルトか判定
            $isSystemDefault = $loan->dealer_loan_plan_id === null
                || $loan->dealer_loan_plan_id === 'default';
    
            $planName = $isSystemDefault
                ? LoanPlanLabel::SYSTEM_DEFAULT
                : ($loan->snapshot_plan_name ?? $loan->dealerLoanPlan?->name ?? LoanPlanLabel::DEALER_PLAN);
    
            return [
                'plan_name'         => $planName,
                'type_label'        => LoanPlanLabel::TYPE_LABELS[$loan->loan_type] ?? LoanPlanLabel::DEFAULT_TYPE,
                'rate'              => $rate,
                'is_default_rate'   => !$loan->snapshot_rate && !$loan->interest_rate,
                'months_options'    => $monthsOptions,
                'min_months'        => $minMonths,
                'max_months'        => max($monthsOptions),
                'down_payment'      => $loan->down_payment ?? 0,
                'misc_fee'          => $loan->misc_fee,
                'bonus_amount'      => $loan->snapshot_bonus_amount ?? 0,
                'bonus_times'       => $loan->snapshot_bonus_times ?? 0,
                'monthly_payment'   => $monthlyPayment,
                'note'              => $loan->note,
                'is_contracted'     => $loan->is_contracted,
                'is_system_default' => $isSystemDefault,
            ];
        })->toArray();
    }

    public function getRejectionCategories(): array
    {
        return ['車両基本情報', '車両スペック', '装備仕様', '画像', 'その他'];
    }

    public function getSpecialTypeOptions(): array
    {
        return $this->record->options
            ->where('option_category', 'special_type')
            ->where('is_equipped', 1)
            ->map(fn ($opt) => SpecialTypeOption::label($opt->option_name))
            ->values()
            ->toArray();
    }

    public function getSalesOptions(): array
    {
        return $this->record->options
            ->where('option_category', 'other')
            ->where('is_equipped', 1)
            ->whereIn('option_name', SalesOption::SALES_OPTIONS)
            ->map(fn ($opt) => SalesOption::label($opt->option_name))
            ->values()
            ->toArray();
    }

    public function getAudioOptions(): array
    {
        return $this->record->options
            ->where('option_category', 'audio')
            ->where('is_equipped', 1)
            ->map(fn ($opt) => str_starts_with($opt->option_name, 'maker_')
                ? 'メーカー：' . str_replace('maker_', '', $opt->option_name)
                : (AudioOption::LABELS[$opt->option_name] ?? $opt->option_name)
            )
            ->values()
            ->toArray();
    }

    public function getNaviOptions(): array
    {
        return $this->record->options
            ->where('option_category', 'navigation')
            ->where('is_equipped', 1)
            ->map(fn ($opt) => NaviOption::LABELS[$opt->option_name] ?? $opt->option_name)
            ->values()
            ->toArray();
    }
}