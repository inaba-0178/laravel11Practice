<?php

declare(strict_types=1);

namespace App\Filament\Pages;

use Filament\Pages\Page;
use App\Filament\Concerns\HasResourcePermission;
use Filament\Forms\Form;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Components\Textarea;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\Section;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Actions\Action;
use Illuminate\Http\Request;
use App\Filament\Resources\DealerReviewResource;
use App\Infrastructure\Eloquent\User\StkDealerReview;
use App\Infrastructure\Eloquent\User\StkDealerReviewReply;
use App\Constants\Role\RoleManagement;
use Filament\Actions\Action as HeaderAction;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;
use App\Filament\Pages\DealerReviewReplyDetail;


class DealerReviewDetail extends Page implements HasTable
{
    use HasResourcePermission;
use InteractsWithForms;
    use InteractsWithTable;

    protected static ?string $navigationIcon           = 'heroicon-o-star';
    protected static string  $view                     = 'filament.pages.dealer-review-detail';
    protected static bool    $shouldRegisterNavigation = false;
    protected static ?string $title                    = '';

    public ?string          $id     = null;
    public ?StkDealerReview $record = null;
    public ?array           $data   = [];

    public function mount(Request $request): void
    {
        $this->id     = $request->input('id');
        $this->record = StkDealerReview::with(['member', 'dealer'])
            ->whereNull('deleted_at')
            ->findOrFail($this->id);

        $user = Auth::user();
        if (in_array($user->role, RoleManagement::REVIEW_ACCESS_ROLES)) {
            if ($this->record->dealer_id !== $user->dealer_id) {
                abort(403);
            }
        }

        $this->form->fill();
    }

    public function getBreadcrumbs(): array
    {
        return [
            DealerReviewResource::getUrl() => '口コミ管理',
            DealerReviewDetail::getUrl(['id' => $this->id]) => '詳細',
        ];
    }

    public function getTitle(): string
    {
        return '口コミ詳細 #' . $this->record->id;
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                StkDealerReviewReply::query()
                    ->where('review_id', $this->id)
                    ->withTrashed()
                    ->with('user')
            )
            ->columns([
                TextColumn::make('responder_name')
                    ->label('送信者名')
                    ->default('-'),

                TextColumn::make('body')
                    ->label('返信内容')
                    ->wrap(),

                TextColumn::make('deleted_reason')
                    ->label('削除理由')
                    ->default('-'),

                TextColumn::make('created_at')
                    ->label('日時')
                    ->dateTime('Y/m/d H:i')
                    ->sortable(),

                TextColumn::make('status')
                    ->label('ステータス')
                    ->getStateUsing(fn ($record) => $record->deleted_at ? '削除済み' : '公開中')
                    ->badge()
                    ->color(fn ($state) => $state === '削除済み' ? 'danger' : 'success'),

            ])
            ->actions([
                Action::make('detail')
                    ->label('詳細')
                    ->color('info')
                    ->url(fn ($record) => DealerReviewReplyDetail::getUrl(['id' => $record->id])),
            ])
            ->defaultSort('created_at', 'asc');
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Textarea::make('body')
                    ->label('返信内容')
                    ->required()
                    ->maxLength(65535)
                    ->rows(4),
            ])
            ->statePath('data');
    }

    public function submitReply(): void
    {
        $data = $this->form->getState();
        $user = Auth::user();

        StkDealerReviewReply::create([
            'review_id'      => $this->record->id,
            'dealer_id'      => $this->record->dealer_id,
            'user_id'        => $user->id,
            'responder_name' => $user->name,
            'body'           => $data['body'],
        ]);

        $this->form->fill();

        Notification::make()
            ->title('返信しました')
            ->success()
            ->send();
    }

    public function infoList(): Infolist
    {
        $ratingLabel = fn (?int $val) => $val
            ? str_repeat('★', $val) . str_repeat('☆', 5 - $val) . " {$val}"
            : '-';

        $data = [
            'nickname'          => $this->record->member?->nickname ?? '-',
            'rating'            => $ratingLabel($this->record->rating),
            'rating_service'    => $ratingLabel($this->record->rating_service),
            'rating_atmosphere' => $ratingLabel($this->record->rating_atmosphere),
            'rating_after'      => $ratingLabel($this->record->rating_after),
            'rating_quality'    => $ratingLabel($this->record->rating_quality),
            'comment'           => $this->record->comment ?? '-',
            'purchased_car'     => $this->record->purchased_car ?? '-',
            'purchased_at'      => $this->record->purchased_at ?? '-',
            'created_at'        => $this->record->created_at,
        ];

        return Infolist::make()
            ->state($data)
            ->schema([
                Section::make('口コミ情報')
                    ->columns(2)
                    ->schema([
                        TextEntry::make('nickname')->label('投稿者'),
                        TextEntry::make('created_at')->label('投稿日時'),
                        TextEntry::make('rating')->label('総合評価'),
                        TextEntry::make('rating_service')->label('接客'),
                        TextEntry::make('rating_atmosphere')->label('雰囲気'),
                        TextEntry::make('rating_after')->label('アフター'),
                        TextEntry::make('rating_quality')->label('品質'),
                        TextEntry::make('purchased_car')->label('購入車種'),
                        TextEntry::make('purchased_at')->label('購入時期'),
                        TextEntry::make('comment')->label('口コミ内容')->columnSpanFull(),
                    ]),
            ]);
    }

    protected function getHeaderActions(): array
    {
        $actions = [];

        if (in_array(Auth::user()->role, RoleManagement::REVIEW_ADMIN_ROLES)) {
            $actions[] = HeaderAction::make('delete')
                ->label('口コミを削除')
                ->color('danger')
                ->requiresConfirmation()
                ->modalHeading('口コミを削除しますか？')
                ->modalDescription('削除すると一般公開されなくなります。')
                ->modalSubmitActionLabel('削除する')
                ->modalCancelActionLabel('キャンセル')
                ->action(function () {
                    $this->record->delete();

                    Notification::make()
                        ->title('口コミを削除しました')
                        ->success()
                        ->send();

                    $this->redirect(DealerReviewResource::getUrl('index'));
                });
        }

        return $actions;
    }
}