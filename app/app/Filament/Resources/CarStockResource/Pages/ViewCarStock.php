<?php

declare(strict_types=1);

namespace App\Filament\Resources\CarStockResource\Pages;

use App\Constants\CarStatus;
use App\Filament\Resources\CarStockResource;
use App\Infrastructure\Eloquent\Mst\MstEquipmentBasic;
use App\Infrastructure\Eloquent\Mst\MstEquipmentDressup;
use App\Infrastructure\Eloquent\Mst\MstEquipmentEnv;
use App\Infrastructure\Eloquent\Mst\MstEquipmentSafety;
use App\Infrastructure\Eloquent\Mst\MstSeatOption;
use App\Infrastructure\Eloquent\Mst\MstVehicleYearVersions;
use App\Infrastructure\Eloquent\User\StkCar;
use Filament\Actions\Action;
use Filament\Forms\Components\DateTimePicker;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Page;
use Illuminate\Support\Facades\Storage;
use App\Filament\Resources\CarRegistrationResource;

class ViewCarStock extends Page
{
    protected static string $resource = CarStockResource::class;
    protected static string $view     = 'filament.pages.view-car-stock';

    public StkCar $record;

    public function mount(StkCar $record): void
    {
        $this->record = $record->load([
            'series',
            'vehicle',
            'manufacturer',
            'detail',
            'images',
            'options',
            'dealer',
            'loans',
            'bodyType',
            'dealerFee',
        ]);
    }

    public function getTitle(): string
    {
        return $this->record->series?->series_name ?? '車両詳細';
    }

    protected function getHeaderActions(): array
    {
        $record = $this->record;

        return [
            // ===== 編集ボタン =====
            Action::make('edit')
                ->label('編集')
                ->color('gray')
                ->icon('heroicon-o-pencil')
                ->requiresConfirmation()
                ->modalHeading('編集ページへ移動しますか？')
                ->modalDescription(new \Illuminate\Support\HtmlString(
                    '<span style="color: #dc2626;">編集を行った場合、再度承認依頼が必要になります。<br>承認後のみ公開可能となりますのでご注意ください。</span>'
                ))
                ->modalSubmitActionLabel('編集ページへ')
                ->modalCancelActionLabel('キャンセル')
                ->action(fn () => redirect(
                    CarRegistrationResource::getUrl('edit', ['record' => $record])
                )),

            // ===== 公開ボタン =====
            Action::make('publish')
                ->label('公開する')
                ->color('success')
                ->icon('heroicon-o-eye')
                ->disabled(fn () => $record->status === CarStatus::AVAILABLE)
                ->tooltip(fn () => $record->status === CarStatus::AVAILABLE ? '既に公開中です' : null)
                ->requiresConfirmation()
                ->modalHeading('この車両を公開しますか？')
                ->modalDescription('公開するとユーザーに表示されます。')
                ->modalSubmitActionLabel('公開する')
                ->modalCancelActionLabel('キャンセル')
                ->action(function () use ($record) {
                    $record->update([
                        'status'          => CarStatus::AVAILABLE,
                        'published_at'    => now(),
                        'publish_end_at'  => null,
                    ]);
                    $this->record = $record->fresh();
                    Notification::make()->title('公開しました')->success()->send();
                }),

            // ===== 公開中止ボタン =====
            Action::make('unpublish')
                ->label('公開中止')
                ->color('warning')
                ->icon('heroicon-o-eye-slash')
                ->disabled(fn () => $record->status !== CarStatus::AVAILABLE)
                ->tooltip(fn () => $record->status !== CarStatus::AVAILABLE ? '公開中の車両のみ中止できます' : null)
                ->requiresConfirmation()
                ->modalHeading('公開を中止しますか？')
                ->modalDescription('公開を中止すると承認済み公開前に戻ります。')
                ->modalSubmitActionLabel('中止する')
                ->modalCancelActionLabel('キャンセル')
                ->action(function () use ($record) {
                    $record->update([
                        'status'          => CarStatus::APPROVED_PENDING,
                        'published_at'    => null,
                        'publish_end_at'  => null,
                    ]);
                    $this->record = $record->fresh();
                    Notification::make()->title('公開を中止しました')->warning()->send();
                }),

            // ===== 期間設定ボタン =====
            Action::make('schedule')
                ->label('期間設定')
                ->color('info')
                ->icon('heroicon-o-clock')
                ->form([
                    DateTimePicker::make('published_at')
                        ->label('公開開始日時')
                        ->required()
                        ->minDate(now())
                        ->default(fn () => $record->published_at ?? now()->addHour()),

                    DateTimePicker::make('publish_end_at')
                        ->label('公開終了日時')
                        ->required()
                        ->minDate(now())
                        ->default(fn () => $record->publish_end_at ?? now()->addMonth())
                        ->after('published_at'),
                ])
                ->action(function (array $data) use ($record) {
                    $record->update([
                        'status'          => CarStatus::SCHEDULED,
                        'published_at'    => $data['published_at'],
                        'publish_end_at'  => $data['publish_end_at'],
                    ]);
                    $this->record = $record->fresh();
                    Notification::make()->title('公開期間を設定しました')->success()->send();
                }),

            // ===== 販売終了ボタン =====
            Action::make('mark_sold')
                ->label('販売終了')
                ->color('danger')
                ->icon('heroicon-o-check-circle')
                ->requiresConfirmation()
                ->modalHeading('販売終了にしますか？')
                ->modalDescription('販売終了にすると公開が停止されます。この操作は取り消せません。')
                ->modalSubmitActionLabel('販売終了にする')
                ->modalCancelActionLabel('キャンセル')
                ->action(function () use ($record) {
                    $record->update([
                        'status'  => CarStatus::SOLD,
                        'sold_at' => now(),
                    ]);
                    $this->record = $record->fresh();
                    Notification::make()->title('販売終了にしました')->danger()->send();
                }),
        ];
    }

    public function getImages(): array
    {
        return $this->record->images
            ->sortBy('display_order')
            ->map(fn ($img) => [
                'id'       => $img->id,
                'url'      => Storage::disk('s3')->url($img->image_url),
                'type'     => $img->image_type,
                'is_main'  => $img->is_main,
            ])
            ->values()
            ->toArray();
    }

    public function getEquipmentSections(): array
    {
        $equipped = $this->record->options
            ->where('is_equipped', true)
            ->pluck('option_name')
            ->toArray();

        $categories = [
            'safety'        => ['label' => '安全装備',     'class' => MstEquipmentSafety::class],
            'basic'         => ['label' => '快適装備',     'class' => MstEquipmentBasic::class],
            'seat'          => ['label' => 'インテリア',   'class' => MstSeatOption::class],
            'dress_up'      => ['label' => 'エクステリア', 'class' => MstEquipmentDressup::class],
            'environmental' => ['label' => '環境装備',     'class' => MstEquipmentEnv::class],
        ];

        $result = [];
        foreach ($categories as $key => $cat) {
            $result[] = [
                'key'   => $key,
                'label' => $cat['label'],
                'items' => $cat['class']::where('is_active', 1)
                    ->orderBy('sort_order')
                    ->get()
                    ->map(fn ($opt) => [
                        'value'       => $opt->value,
                        'label'       => $opt->label,
                        'is_equipped' => in_array($opt->value, $equipped),
                    ])
                    ->toArray(),
            ];
        }

        return $result;
    }

    public function getVehicleSpec(): ?array
    {
        $version = MstVehicleYearVersions::where('vehicle_id', $this->record->vehicle_id)->first();
        if (!$version) return null;

        return [
            'vehicle'        => $this->record->vehicle,
            'vehicleVersion' => $version,
        ];
    }

    public function getLoan(): ?array
    {
        $loan = $this->record->loans->first();
        if (!$loan) return null;

        return [
            'plan_name'      => $loan->snapshot_plan_name ?? 'システムデフォルト',
            'interest_rate'  => $loan->snapshot_rate,
            'min_months'     => $loan->snapshot_min_months,
            'max_months'     => $loan->snapshot_max_months,
            'down_payment'   => $loan->down_payment ?? 0,
            'bonus_amount'   => $loan->snapshot_bonus_amount ?? 0,
        ];
    }

    public function hasOption(string $value): bool
    {
        return $this->record->options
            ->where('is_equipped', true)
            ->pluck('option_name')
            ->contains($value);
    }

    public function formatRepairHistory(string $value): string
    {
        return match($value) {
            'none'    => 'なし',
            'minor'   => '軽微あり',
            'major'   => 'あり',
            default   => '不明',
        };
    }

    public function formatInspection(?string $status, mixed $expireDate): string
    {
        return match($status) {
            'available' => $expireDate ? '車検整備付（' . $expireDate->format('Y/m') . '）' : '車検整備付',
            'none'      => '車検なし',
            'new_car'   => '新車',
            default     => '-',
        };
    }

    public function formatSteering(?string $value): string
    {
        return match($value) {
            'right' => '右ハンドル',
            'left'  => '左ハンドル',
            default => '-',
        };
    }

    public function formatSlideDoor(?string $value): string
    {
        return match($value) {
            'none'         => 'なし',
            'right_only'   => '右のみ',
            'both_manual'  => '両側手動',
            'both_power'   => '両側電動',
            'right_power'  => '右電動',
            'left_power'   => '左電動',
            default        => '-',
        };
    }

    public function formatFuelType(?string $value): string
    {
        return match($value) {
            'gasoline' => 'ガソリン',
            'diesel'   => 'ディーゼル',
            'hybrid'   => 'ハイブリッド',
            'electric' => '電気',
            'phev'     => 'PHEV',
            'other'    => 'その他',
            default    => '-',
        };
    }
}