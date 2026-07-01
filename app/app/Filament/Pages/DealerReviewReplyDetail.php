<?php

declare(strict_types=1);

namespace App\Filament\Pages;

use Filament\Pages\Page;
use App\Filament\Concerns\HasResourcePermission;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\Section;
use Illuminate\Http\Request;
use App\Infrastructure\Eloquent\User\StkDealerReviewReply;
use App\Constants\Role\RoleManagement;
use Illuminate\Support\Facades\Auth;
use App\Filament\Resources\DealerReviewResource;
use Filament\Actions\Action as HeaderAction;
use Filament\Notifications\Notification;
use App\Filament\Pages\DealerReviewDetail;

class DealerReviewReplyDetail extends Page
{
    use HasResourcePermission;
    protected static ?string $navigationIcon           = 'heroicon-o-chat-bubble-left';
    protected static string  $view                     = 'filament.pages.dealer-review-reply-detail';
    protected static bool    $shouldRegisterNavigation = false;
    protected static ?string $title                    = '';

    public ?string                $id     = null;
    public ?StkDealerReviewReply  $record = null;

    public function mount(Request $request): void
    {
        $this->id     = $request->input('id');
        $this->record = StkDealerReviewReply::withTrashed()
            ->with(['user', 'review', 'deletedBy'])
            ->findOrFail($this->id);

        $user = Auth::user();
        if (in_array($user->role, RoleManagement::DEALER_ROLES)) {
            if ($this->record->dealer_id !== $user->dealer_id) {
                abort(403);
            }
        }
    }

    public function getBreadcrumbs(): array
    {
        return [
            DealerReviewResource::getUrl() => '口コミ管理',
            DealerReviewDetail::getUrl(['id' => $this->record->review_id]) => '口コミ詳細',
            DealerReviewReplyDetail::getUrl(['id' => $this->id]) => '返信詳細',
        ];
    }

    public function getTitle(): string
    {
        return '返信詳細 #' . $this->record->id;
    }

    public function infoList(): Infolist
    {
        $data = [
            'id'                => $this->record->id,
            'responder_name'    => $this->record->responder_name ?? '-',
            'status'            => $this->record->deleted_at ? '削除済み' : '公開中',
            'body'              => $this->record->body,
            'created_at'        => $this->record->created_at,
            'deleted_reason'    => $this->record->deleted_reason ?? '-',
            'deleted_at'        => $this->record->deleted_at ?? '-',
            'deleted_by'        => $this->record->deletedBy?->name ?? '-',
        ];

        return Infolist::make()
            ->state($data)
            ->schema([
                Section::make('返信情報')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('id')->label('ID'),
                        TextEntry::make('responder_name')->label('対応者'),
                        TextEntry::make('status')
                            ->label('ステータス')
                            ->badge()
                            ->color(fn ($state) => $state === '削除済み' ? 'danger' : 'success'),
                        TextEntry::make('created_at')->label('日時'),
                        TextEntry::make('body')->label('送信内容')->columnSpanFull(),
                    ]),

                Section::make('削除情報')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('deleted_by')->label('削除者')->columnSpanFull(),
                        TextEntry::make('deleted_reason')->label('削除理由'),
                        TextEntry::make('deleted_at')->label('削除日時'),
                    ]),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            HeaderAction::make('delete')
                ->label('削除')
                ->color('danger')
                ->visible(fn () => !$this->record->deleted_at)
                ->form([
                    \Filament\Forms\Components\Textarea::make('deleted_reason')
                        ->label('削除理由（任意）')
                        ->maxLength(255),
                ])
                ->requiresConfirmation()
                ->modalHeading('返信を削除しますか？')
                ->modalDescription('削除後も管理ツール上で内容を確認できます。')
                ->modalSubmitActionLabel('削除する')
                ->modalCancelActionLabel('キャンセル')
                ->action(function (array $data) {
                    $this->record->update([
                        'deleted_reason' => $data['deleted_reason'] ?? null,
                        'deleted_by'     => Auth::id(),
                    ]);
                    $this->record->delete();

                    // 再読み込み
                    $this->record = StkDealerReviewReply::withTrashed()
                        ->with(['user', 'review', 'deletedBy'])
                        ->findOrFail($this->id);

                    Notification::make()
                        ->title('返信を削除しました')
                        ->success()
                        ->send();
                }),
        ];
    }
}