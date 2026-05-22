<?php

declare(strict_types=1);

namespace App\Filament\Resources\BulkCarApprovalResource\Pages;

use App\Application\Services\MailService;
use App\Constants\CarStatus;
use App\Domain\CarUpload\Services\BulkCarImportService;
use App\Domain\Shared\Constants\MailTemplateKey;
use App\Filament\Resources\BulkCarApprovalResource;
use App\Infrastructure\Eloquent\User\StkBulkUploadBatch;
use App\Infrastructure\Eloquent\User\StkCar;
use App\Infrastructure\Eloquent\User\StkCarImages;
use App\Infrastructure\Eloquent\Mst\MstEquipmentSafety;
use App\Infrastructure\Eloquent\Mst\MstEquipmentBasic;
use App\Infrastructure\Eloquent\Mst\MstSeatOption;
use App\Infrastructure\Eloquent\Mst\MstEquipmentDressup;
use App\Infrastructure\Eloquent\Mst\MstEquipmentEnv;
use App\Models\User;
use App\Notifications\CarRegistrationStatusNotification;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Page;
use Illuminate\Support\Facades\Storage;
use Filament\Actions\Action;

class ViewBulkCarApproval extends Page
{
    protected static string $resource = BulkCarApprovalResource::class;
    protected static string $view     = 'filament.pages.bulk-car-approval-view';

    public StkBulkUploadBatch $record;

    /** 現在表示中の車両ID */
    public int $currentCarId = 0;

    /** 差し戻し入力（車両IDをキーにした配列） */
    public array $generalComments = [];
    public array $flaggedImages   = [];
    public array $rejectionItems  = [];

    /** チェックボックスで選択した車両ID */
    public array $selectedCarIds = [];

    public function getTitle(): string
    {
        return '一括車両承認';
    }

    public function getBreadcrumb(): string
    {
        return '一括車両承認';
    }

    public function mount(StkBulkUploadBatch $record): void
    {
        $this->record = $record->load(['cars.series', 'cars.detail', 'cars.images', 'cars.options', 'dealer']);

        // 最初の車両を表示
        $firstCar = $this->record->cars->where('status', CarStatus::PENDING)->first()
            ?? $this->record->cars->first();

        if ($firstCar) {
            $this->currentCarId = $firstCar->id;
        }

        // 既存の差し戻し内容を読み込み
        foreach ($this->record->cars as $car) {
            if ($car->rejection_reason && is_array($car->rejection_reason)) {
                $this->generalComments[$car->id] = $car->rejection_reason['general_comment'] ?? '';
                $this->flaggedImages[$car->id]   = $car->rejection_reason['flagged_images'] ?? [];
                $this->rejectionItems[$car->id]  = $car->rejection_reason['items'] ?? [];
            } else {
                $this->generalComments[$car->id] = '';
                $this->flaggedImages[$car->id]   = [];
                $this->rejectionItems[$car->id]  = [];
            }
        }
    }

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\Action::make('back')
                ->label('一覧に戻る')
                ->color('gray')
                ->url(ListBulkCarApprovals::getUrl()),

            \Filament\Actions\Action::make('approveAll')
                ->label('一括承認')
                ->modalHeading('一括承認の確認')
                ->modalDescription('承認待ちの全車両を承認済み（公開前）にします。よろしいですか？')
                ->modalSubmitActionLabel('承認する')
                ->color('success')
                ->action(fn () => $this->approveAll()),

            // \Filament\Actions\Action::make('rejectSelected')
            //     ->label('選択差し戻し')
            //     ->modalHeading('選択差し戻しの確認')
            //     ->modalDescription('チェックした車両を差し戻します。よろしいですか？')
            //     ->modalSubmitActionLabel('差し戻す')
            //     ->color('warning')
            //     ->action(fn () => $this->rejectSelected()),

            \Filament\Actions\Action::make('rejectAll')
                ->label('一括差し戻し')
                ->modalHeading('一括差し戻しの確認')
                ->modalDescription('全車両を差し戻します。よろしいですか？')
                ->modalSubmitActionLabel('差し戻す')
                ->color('danger')
                ->action(fn () => $this->rejectAll()),

            \Filament\Actions\Action::make('publishAll')
                ->label('公開')
                ->modalHeading('公開の確認')
                ->modalDescription('承認済みの全車両を公開します。公開するとユーザーに表示されます。よろしいですか？')
                ->modalSubmitActionLabel('公開する')
                ->color('info')
                ->disabled(fn () => !$this->record->cars->every(
                    fn($c) => $c->status === \App\Constants\CarStatus::APPROVED_PENDING
                ))
                ->action(fn () => $this->publishAll()),
        ];
    }

    // ===== タブ切り替え =====
    public function selectCar(int $carId): void
    {
        $this->currentCarId = $carId;
    }

    public function getCurrentCar(): ?StkCar
    {
        return $this->record->cars->firstWhere('id', $this->currentCarId);
    }

    // ===== 画像指摘 =====
    public function toggleImageFlag(int $imageId): void
    {
        $carId    = $this->currentCarId;
        $existing = collect($this->flaggedImages[$carId] ?? [])->firstWhere('id', $imageId);

        if ($existing) {
            $this->flaggedImages[$carId] = collect($this->flaggedImages[$carId])
                ->filter(fn ($img) => $img['id'] !== $imageId)
                ->values()->toArray();
        } else {
            $this->flaggedImages[$carId][] = [
                'id'              => $imageId,
                'reason'          => '',
                'dealer_response' => '',
                'resolved'        => false,
            ];
        }
    }

    public function updateImageReason(int $imageId, string $reason): void
    {
        $carId = $this->currentCarId;
        $this->flaggedImages[$carId] = collect($this->flaggedImages[$carId] ?? [])
            ->map(fn ($img) => $img['id'] === $imageId ? array_merge($img, ['reason' => $reason]) : $img)
            ->toArray();
    }

    // ===== 項目指摘 =====
    public function addRejectionItem(): void
    {
        $this->rejectionItems[$this->currentCarId][] = [
            'category'        => '',
            'reason'          => '',
            'dealer_response' => '',
            'resolved'        => false,
        ];
    }

    public function removeRejectionItem(int $index): void
    {
        array_splice($this->rejectionItems[$this->currentCarId], $index, 1);
    }

    public function updateRejectionItem(int $index, string $field, string $value): void
    {
        if (isset($this->rejectionItems[$this->currentCarId][$index])) {
            $this->rejectionItems[$this->currentCarId][$index][$field] = $value;
        }
    }

    // ===== チェックボックス =====
    public function toggleCarSelection(int $carId): void
    {
        if (in_array($carId, $this->selectedCarIds)) {
            $this->selectedCarIds = array_filter(
                $this->selectedCarIds,
                fn ($id) => $id !== $carId
            );
        } else {
            // 差し戻し内容が記載済みの場合のみ選択可能
            $hasContent = !empty($this->generalComments[$carId])
                || !empty($this->flaggedImages[$carId])
                || !empty($this->rejectionItems[$carId]);

            if ($hasContent) {
                $this->selectedCarIds[] = $carId;
            } else {
                Notification::make()
                    ->title('差し戻し内容を入力してからチェックしてください')
                    ->warning()
                    ->send();
            }
        }
    }

    // ===== 一括承認 =====
    public function approveAll(): void
    {
        $targetCars = $this->record->cars->whereIn('status', [
            CarStatus::PENDING,
            CarStatus::APPROVED_PENDING,
            CarStatus::REJECTED,
        ]);

        foreach ($targetCars as $car) {
            $car->update([
                'status'           => CarStatus::APPROVED_PENDING,
                'rejection_reason' => null,
                'published_at'     => null,
            ]);

            $this->sendDealerNotification(
                car:         $car,
                templateKey: MailTemplateKey::CAR_REGISTRATION_APPROVED_DEALER,
                placeholders: [
                    'dealer_name'   => $car->dealer?->name ?? '',
                    'car_name'      => $car->series?->series_name ?? '',
                    'registered_at' => $car->created_at?->format('Y/m/d H:i'),
                    'approved_at'   => now()->format('Y/m/d H:i'),
                    'admin_comment' => '',
                ],
                status:  'approved',
                message: '車両が承認されました。公開までしばらくお待ちください。',
            );
        }

        app(BulkCarImportService::class)->updateBatchCounts($this->record->id);
        $this->record->load(['cars.series', 'cars.detail', 'cars.images', 'cars.options', 'dealer']);
        Notification::make()->title('一括承認しました（公開前）')->success()->send();
    }

    // ===== 選択差し戻し =====
    public function rejectSelected(): void
    {
        if (empty($this->selectedCarIds)) {
            Notification::make()->title('差し戻す車両を選択してください')->warning()->send();
            return;
        }

        foreach ($this->selectedCarIds as $carId) {
            $car = $this->record->cars->firstWhere('id', $carId);
            if (!$car) continue;
            $this->rejectCar($car);
        }

        $this->selectedCarIds = [];
        app(BulkCarImportService::class)->updateBatchCounts($this->record->id);
        $this->record->load(['cars.series', 'cars.detail', 'cars.images', 'cars.options', 'dealer']);
        Notification::make()->title('選択した車両を差し戻しました')->danger()->send();
    }

    // ===== 一括差し戻し =====
    public function rejectAll(): void
    {
        $targetCars = $this->record->cars->whereIn('status', [
            CarStatus::PENDING,
            CarStatus::APPROVED_PENDING,
            CarStatus::AVAILABLE,
        ]);

        foreach ($targetCars as $car) {
            $car->update([
                'status'           => CarStatus::REJECTED,
                'rejection_reason' => [
                    'general_comment' => $this->generalComments[$car->id] ?? '',
                    'flagged_images'  => $this->flaggedImages[$car->id] ?? [],
                    'items'           => $this->rejectionItems[$car->id] ?? [],
                ],
            ]);

            $this->sendDealerNotification(
                car:         $car,
                templateKey: MailTemplateKey::CAR_REGISTRATION_REJECTED_DEALER,
                placeholders: [
                    'dealer_name'   => $car->dealer?->name ?? '',
                    'car_name'      => $car->series?->series_name ?? '',
                    'registered_at' => $car->created_at?->format('Y/m/d H:i'),
                    'rejected_at'   => now()->format('Y/m/d H:i'),
                    'reject_reason' => $this->generalComments[$car->id] ?? '',
                ],
                status:  'rejected',
                message: '車両が差し戻されました。指摘内容をご確認ください。',
            );
        }

        app(BulkCarImportService::class)->updateBatchCounts($this->record->id);
        $this->record->load(['cars.series', 'cars.detail', 'cars.images', 'cars.options', 'dealer']);
        Notification::make()->title('一括差し戻ししました')->danger()->send();
    }

    // ===== 一括公開 =====
    public function publishAll(): void
    {
        $targetCars = $this->record->cars->where('status', CarStatus::APPROVED_PENDING);

        if ($targetCars->isEmpty()) {
            Notification::make()->title('公開できる車両がありません')->warning()->send();
            return;
        }

        foreach ($targetCars as $car) {
            $car->update([
                'status'       => CarStatus::AVAILABLE,
                'published_at' => now(),
            ]);
        }

        app(BulkCarImportService::class)->updateBatchCounts($this->record->id);
        $this->record->load(['cars.series', 'cars.detail', 'cars.images', 'cars.options', 'dealer']);
        Notification::make()->title('一括公開しました')->success()->send();
    }

    private function rejectCar(StkCar $car): void
    {
        $carId           = $car->id;
        $generalComment  = $this->generalComments[$carId] ?? '';
        $flaggedImages   = $this->flaggedImages[$carId] ?? [];
        $rejectionItems  = $this->rejectionItems[$carId] ?? [];

        $car->update([
            'status'           => CarStatus::REJECTED,
            'rejection_reason' => [
                'general_comment' => $generalComment,
                'flagged_images'  => $flaggedImages,
                'items'           => $rejectionItems,
            ],
        ]);

        $itemText = collect($rejectionItems)
            ->map(fn ($item) => "[{$item['category']}] {$item['reason']}")
            ->join("\n");

        $this->sendDealerNotification(
            car:         $car,
            templateKey: MailTemplateKey::CAR_REGISTRATION_REJECTED_DEALER,
            placeholders: [
                'dealer_name'   => $car->dealer?->name ?? '',
                'car_name'      => $car->series?->series_name ?? '',
                'registered_at' => $car->created_at?->format('Y/m/d H:i'),
                'rejected_at'   => now()->format('Y/m/d H:i'),
                'reject_reason' => $generalComment . "\n" . $itemText,
            ],
            status:  'rejected',
            message: '車両が差し戻されました。指摘内容をご確認ください。',
        );
    }

    private function sendDealerNotification(StkCar $car, string $templateKey, array $placeholders, string $status, string $message): void
    {
        $dealerEmail = $car->dealer?->email;
        if ($dealerEmail) {
            try {
                app(MailService::class)->send(
                    templateKey:  $templateKey,
                    toEmail:      $dealerEmail,
                    placeholders: $placeholders
                );
            } catch (\Throwable $e) {
                \Log::error('BulkCarApproval mail error', ['error' => $e->getMessage()]);
            }
        }

        User::where('dealer_id', $car->dealer_id)
            ->where('is_active', 1)
            ->get()
            ->each(fn (User $u) => $u->notify(
                new CarRegistrationStatusNotification(
                    carName: $car->series?->series_name ?? '不明',
                    status:  $status,
                    message: $message,
                    carId:   $car->id,
                )
            ));
    }

    // ===== 表示用ヘルパー =====
    public function getImages(): array
    {
        $car = $this->getCurrentCar();
        if (!$car) return [];

        return StkCarImages::where('car_id', $car->id)
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
        return collect($this->flaggedImages[$this->currentCarId] ?? [])->contains('id', $imageId);
    }

    public function getRejectionCategories(): array
    {
        return ['車両基本情報', '車両スペック', '装備仕様', '画像', 'その他'];
    }

    public function getEquipmentByCategory(): array
    {
        $car = $this->getCurrentCar();
        if (!$car) return [];

        $equipped = collect($car->options)
            ->where('is_equipped', 1)
            ->pluck('option_name')
            ->toArray();

        $categories = [
            'safety'        => ['label' => '安全装備',     'class' => MstEquipmentSafety::class,  'items' => []],
            'basic'         => ['label' => '快適装備',     'class' => MstEquipmentBasic::class,   'items' => []],
            'seat'          => ['label' => 'インテリア',   'class' => MstSeatOption::class,        'items' => []],
            'dress_up'      => ['label' => 'エクステリア', 'class' => MstEquipmentDressup::class,  'items' => []],
            'environmental' => ['label' => '環境装備',     'class' => MstEquipmentEnv::class,      'items' => []],
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

    /**
     * 単体承認
     */
    public function approveCar(): void
    {
        $car = $this->getCurrentCar();
        if (!$car) return;

        $car->update([
            'status'           => CarStatus::APPROVED_PENDING,
            'rejection_reason' => null,
            'published_at'     => null,
        ]);

        $this->sendDealerNotification(
            car:         $car,
            templateKey: MailTemplateKey::CAR_REGISTRATION_APPROVED_DEALER,
            placeholders: [
                'dealer_name'   => $car->dealer?->name ?? '',
                'car_name'      => $car->series?->series_name ?? '',
                'registered_at' => $car->created_at?->format('Y/m/d H:i'),
                'approved_at'   => now()->format('Y/m/d H:i'),
                'admin_comment' => '',
            ],
            status:  'approved',
            message: '車両が承認されました。公開までしばらくお待ちください。',
        );

        app(BulkCarImportService::class)->updateBatchCounts($this->record->id);
        $this->record = $this->record->fresh(['cars.series', 'cars.detail', 'cars.images', 'cars.options', 'dealer']);

        Notification::make()->title('承認しました（公開前）')->success()->send();
    }

    /**
     * 単体差し戻し
     */
    public function rejectCurrent(): void
    {
        $car = $this->getCurrentCar();
        if (!$car) return;

        $carId          = $car->id;
        $generalComment = $this->generalComments[$carId] ?? '';
        $flagged        = $this->flaggedImages[$carId] ?? [];
        $items          = $this->rejectionItems[$carId] ?? [];

        if (empty($generalComment) && empty($flagged) && empty($items)) {
            Notification::make()->title('指摘内容を入力してください')->danger()->send();
            return;
        }

        $this->rejectCar($car);

        app(BulkCarImportService::class)->updateBatchCounts($this->record->id);

        // recordを再取得してタブを更新
        $this->record = $this->record->fresh(['cars.series', 'cars.detail', 'cars.images', 'cars.options', 'dealer']);

        Notification::make()->title('差し戻しました')->danger()->send();
    }
}