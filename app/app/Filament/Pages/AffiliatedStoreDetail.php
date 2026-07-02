<?php

declare(strict_types=1);

namespace App\Filament\Pages;

use Filament\Pages\Page;
use App\Filament\Concerns\HasResourcePermission;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\Section;
use Illuminate\Http\Request;
use App\Filament\Resources\AffiliatedStoreResource;
use App\Infrastructure\Eloquent\User\StkAffiliatedStore;
use App\Constants\Role\RoleManagement;
use Filament\Actions\Action;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;
use App\Constants\AffiliatedStoreStatus;
use App\Constants\AffiliatedStoreType;

class AffiliatedStoreDetail extends Page
{
    use HasResourcePermission;
    protected static ?string $navigationIcon           = 'heroicon-o-building-storefront';
    protected static string  $view                     = 'filament.pages.affiliated-store-detail';
    protected static bool    $shouldRegisterNavigation = false;
    protected static ?string $title                    = '';

    public ?string              $id     = null;
    public ?StkAffiliatedStore  $record = null;

    public function mount(Request $request): void
    {
        $this->id     = $request->input('id');
        $this->record = StkAffiliatedStore::with([
                'dealer',
                'affiliatedDealer',
                'requestedBy',
                'approvedBy',
                'dissolvedBy',
            ])
            ->findOrFail($this->id);

        $user = Auth::user();
        if (in_array($user->role, RoleManagement::AFFILIATED_STORE_ACCESS_ROLES)) {
            if ($this->record->dealer_id !== $user->dealer_id
                && $this->record->affiliated_dealer_id !== $user->dealer_id) {
                abort(403);
            }
        }
    }

    public function getBreadcrumbs(): array
    {
        return [
            AffiliatedStoreResource::getUrl() => '系列店・提携店管理',
            AffiliatedStoreDetail::getUrl(['id' => $this->id]) => '詳細',
        ];
    }

    public function getTitle(): string
    {
        return '申請詳細 #' . $this->record->id;
    }

    public function infoList(): Infolist
    {
        $user           = Auth::user();
        $isRequester    = $this->record->dealer_id === $user->dealer_id;
        $targetDealer   = $isRequester ? $this->record->affiliatedDealer : $this->record->dealer;

        $data = [
            'id'                => $this->record->id,
            'type'              => AffiliatedStoreType::label($this->record->type),
            'status'            => AffiliatedStoreStatus::label($this->record->status),
            'requested_by'      => $this->record->requestedBy?->name ?? '-',
            'requested_at'      => $this->record->requested_at,
            'dealer_name'       => $targetDealer?->name ?? '-',
            'dealer_address'    => ($targetDealer?->city ?? '') . ($targetDealer?->address_detail ?? ''),
            'dealer_phone'      => $targetDealer?->phone ?? '-',
            'approved_by'       => $this->record->approvedBy?->name ?? '-',
            'approved_at'       => $this->record->approved_at ?? '-',
            'rejected_reason'   => $this->record->rejected_reason ?? '-',
            'dissolved_by'      => $this->record->dissolvedBy?->name ?? '-',
            'dissolved_at'      => $this->record->dissolved_at ?? '-',
            'dissolved_reason'  => $this->record->dissolved_reason ?? '-',
        ];

        return Infolist::make()
            ->state($data)
            ->schema([
                Section::make('申請情報')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('id')->label('ID'),
                        TextEntry::make('type')->label('種別'),
                        TextEntry::make('status')
                            ->label('ステータス')
                            ->badge()
                            ->color(fn ($state) => AffiliatedStoreStatus::color($state)),
                        TextEntry::make('requested_by')->label('申請者'),
                        TextEntry::make('requested_at')->label('申請日時'),
                    ]),

                Section::make('相手店舗情報')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('dealer_name')->label('店舗名'),
                        TextEntry::make('dealer_phone')->label('電話番号'),
                        TextEntry::make('dealer_address')->label('住所')->columnSpanFull(),
                    ]),

                Section::make('承認情報')
                    ->columns(2)
                    ->visible(fn () => $this->record->status === AffiliatedStoreStatus::APPROVED)
                    ->schema([
                        TextEntry::make('approved_by')->label('承認者'),
                        TextEntry::make('approved_at')->label('承認日時'),
                    ]),

                Section::make('拒否情報')
                    ->columns(2)
                    ->visible(fn () => $this->record->status === AffiliatedStoreStatus::REJECTED)
                    ->schema([
                        TextEntry::make('rejected_reason')->label('拒否理由')->columnSpanFull(),
                    ]),

                Section::make('解除情報')
                    ->columns(2)
                    ->visible(fn () => $this->record->status === AffiliatedStoreStatus::DISSOLVED)
                    ->schema([
                        TextEntry::make('dissolved_by')->label('解除者'),
                        TextEntry::make('dissolved_at')->label('解除日時'),
                        TextEntry::make('dissolved_reason')->label('解除理由')->columnSpanFull(),
                    ]),
            ]);
    }

    protected function getHeaderActions(): array
    {
        $user    = Auth::user();
        $actions = [];

        // 申請先ディーラーのみ承認・拒否可能
        if ($this->record->status === 'pending'
            && $this->record->affiliated_dealer_id === $user->dealer_id) {

            $actions[] = Action::make('approve')
                ->label('承認する')
                ->color('success')
                ->requiresConfirmation()
                ->modalHeading('申請を承認しますか？')
                ->modalDescription('承認するとお互いの系列店・提携店一覧に表示されます。')
                ->modalSubmitActionLabel('承認する')
                ->modalCancelActionLabel('キャンセル')
                ->action(function () use ($user) {
                    $this->record->update([
                        'status'      => 'approved',
                        'approved_by' => $user->id,
                        'approved_at' => now(),
                    ]);

                    $this->reloadRecord();

                    Notification::make()
                        ->title('承認しました')
                        ->success()
                        ->send();

                    $this->redirect(AffiliatedStoreDetail::getUrl(['id' => $this->id]));
                });

            $actions[] = Action::make('reject')
                ->label('拒否する')
                ->color('danger')
                ->form([
                    Textarea::make('rejected_reason')
                        ->label('拒否理由')
                        ->required()
                        ->maxLength(255),
                ])
                ->requiresConfirmation()
                ->modalHeading('申請を拒否しますか？')
                ->modalSubmitActionLabel('拒否する')
                ->modalCancelActionLabel('キャンセル')
                ->action(function (array $data) {
                    $this->record->update([
                        'status'          => 'rejected',
                        'rejected_reason' => $data['rejected_reason'],
                    ]);

                    $this->reloadRecord();

                    Notification::make()
                        ->title('拒否しました')
                        ->danger()
                        ->send();

                    $this->redirect(AffiliatedStoreDetail::getUrl(['id' => $this->id]));
                });
        }

        // 申請元は pending でも取り消し可能、approved でも解除可能
        if (AffiliatedStoreStatus::isActive($this->record->status)
            && in_array($user->dealer_id, [
                $this->record->dealer_id,
                $this->record->affiliated_dealer_id,
            ])) {

            $label   = $this->record->status === AffiliatedStoreStatus::PENDING ? '申請を取り消す' : '解除する';
            $heading = $this->record->status === AffiliatedStoreStatus::PENDING? '申請を取り消しますか？' : '系列店・提携店関係を解除しますか？';
            $desc    = $this->record->status === AffiliatedStoreStatus::PENDING
                ? '申請を取り消します。'
                : '解除するとお互いの一覧から削除されます。';

            $actions[] = Action::make('dissolve')
                ->label($label)
                ->color('warning')
                ->form([
                    Textarea::make('dissolved_reason')
                        ->label($this->record->status === 'pending' ? '取り消し理由' : '解除理由')
                        ->required()
                        ->maxLength(255),
                ])
                ->requiresConfirmation()
                ->modalHeading($heading)
                ->modalDescription($desc)
                ->modalSubmitActionLabel($label)
                ->modalCancelActionLabel('キャンセル')
                ->action(function (array $data) use ($user) {
                    $previousStatus = $this->record->status;

                    $this->record->update([
                        'status'           => 'dissolved',
                        'dissolved_by'     => $user->id,
                        'dissolved_at'     => now(),
                        'dissolved_reason' => $data['dissolved_reason'] ?? null,
                    ]);

                    $this->reloadRecord();

                    Notification::make()
                        ->title($previousStatus === AffiliatedStoreStatus::PENDING ? '申請を取り消しました' : '解除しました')
                        ->warning()
                        ->send();

                    $this->redirect(AffiliatedStoreDetail::getUrl(['id' => $this->id]));
                });
        }

        // super/adminは強制解除可能
        if (in_array($user->role, RoleManagement::AFFILIATED_STORE_FORCE_DISSOLVE_ROLES)
            && $this->record->status === 'approved') {

            $actions[] = Action::make('force_dissolve')
                ->label('強制解除')
                ->color('danger')
                ->form([
                    Textarea::make('dissolved_reason')
                        ->label('強制解除理由')
                        ->required()
                        ->maxLength(255),
                ])
                ->requiresConfirmation()
                ->modalHeading('強制解除しますか？')
                ->modalDescription('管理者権限で強制的に解除します。理由は記録されます。')
                ->modalSubmitActionLabel('強制解除する')
                ->modalCancelActionLabel('キャンセル')
                ->action(function (array $data) use ($user) {
                    $this->record->update([
                        'status'           => 'dissolved',
                        'dissolved_by'     => $user->id,
                        'dissolved_at'     => now(),
                        'dissolved_reason' => '【強制解除】' . $data['dissolved_reason'],
                    ]);

                    $this->reloadRecord();

                    Notification::make()
                        ->title('強制解除しました')
                        ->danger()
                        ->send();

                    $this->redirect(AffiliatedStoreDetail::getUrl(['id' => $this->id]));
                });
        }

        return $actions;
    }

    private function reloadRecord(): void
    {
        $this->record = StkAffiliatedStore::with([
            'dealer',
            'affiliatedDealer',
            'requestedBy',
            'approvedBy',
            'dissolvedBy',
        ])->findOrFail($this->id);
    }
}