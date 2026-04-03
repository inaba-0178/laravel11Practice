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
        $car     = $this->record->load(['series', 'detail', 'options', 'dealer']);
        $series  = MstCarSeries::find($car->series_id);
        $vehicle = MstVehicles::find($car->vehicle_id);
        return ['car' => $car, 'series' => $series, 'vehicle' => $vehicle];
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
        $categories = CarOptionCategory::LABELS;

        $options = $this->record->options
            ->whereIn('option_category', array_keys($categories))
            ->where('is_equipped', 1)
            ->groupBy('option_category');

        $result = [];
        foreach ($categories as $key => $label) {
            if ($options->has($key) && $options[$key]->count() > 0) {
                $result[$key] = [
                    'label' => $label,
                    'items' => $options[$key]->pluck('option_name')->toArray(),
                ];
            }
        }

        return $result;
    }

    public function getLoans(): array
    {
        return $this->record->loans->map(function ($loan) {
            $monthlyPayment = $loan->calcMonthlyPayment($this->record->price);
            return [
                'type_label'     => \App\Infrastructure\Eloquent\User\StkCarLoan::TYPE_LABELS[$loan->loan_type] ?? $loan->loan_type,
                'interest_rate'  => $loan->interest_rate ?? \App\Infrastructure\Eloquent\User\StkCarLoan::DEFAULT_INTEREST_RATE,
                'is_default_rate'=> $loan->interest_rate === null,
                'loan_months'    => $loan->loan_months ?? \App\Infrastructure\Eloquent\User\StkCarLoan::DEFAULT_LOAN_MONTHS,
                'is_default_months' => $loan->loan_months === null,
                'down_payment'   => $loan->down_payment ?? 0,
                'misc_fee'       => $loan->misc_fee,
                'residual_value' => $loan->residual_value,
                'monthly_payment'=> $monthlyPayment ? round($monthlyPayment) : null,
                'note'           => $loan->note,
                'loan_type'      => $loan->loan_type,
            ];
        })->toArray();
    }
    public function getRejectionCategories(): array
    {
        return ['車両基本情報', '車両スペック', '装備仕様', '画像', 'その他'];
    }
}