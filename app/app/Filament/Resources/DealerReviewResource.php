<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Filament\Resources\DealerReviewResource\Pages\ListDealerReviews;
use App\Filament\Pages\DealerReviewDetail;
use App\Infrastructure\Eloquent\User\StkDealerReview;
use App\Constants\NavigationSort;
use App\Constants\NavigationGroup;
use App\Constants\RoleConstants;
use App\Constants\Role\RoleManagement;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Actions\Action;
use Filament\Tables\Enums\ActionsPosition;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder;

class DealerReviewResource extends Resource
{
    protected static ?string $model            = StkDealerReview::class;
    protected static ?string $navigationIcon   = 'heroicon-o-star';
    protected static ?string $navigationGroup  = NavigationGroup::DEALER_GROUP->value;
    protected static ?int    $navigationSort   = NavigationSort::DEALER_REVIEW->value;
    protected static ?string $pluralModelLabel = '口コミ管理';
    protected static ?string $modelLabel       = '口コミ';

    public static function canAccess(): bool
    {
        return in_array(Auth::user()?->role, RoleManagement::REVIEW_ACCESS_ROLES);
    }

    public static function getEloquentQuery(): Builder
    {
        $user  = Auth::user();
        $query = parent::getEloquentQuery()->with(['member', 'activeReplies']);

        if (in_array($user->role, RoleManagement::DEALER_ROLES)) {
            $query->where('dealer_id', $user->dealer_id);
        }

        return $query;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),

                TextColumn::make('member.nickname')
                    ->label('投稿者')
                    ->default('-'),

                TextColumn::make('rating')
                    ->label('総合評価')
                    ->formatStateUsing(fn ($state) => str_repeat('★', $state) . str_repeat('☆', 5 - $state))
                    ->sortable(),

                TextColumn::make('comment')
                    ->label('口コミ内容')
                    ->limit(50),

                TextColumn::make('active_replies_count')
                    ->label('返信')
                    ->counts('activeReplies')
                    ->formatStateUsing(fn ($state) => $state > 0 ? "返信済み({$state}件)" : '未返信')
                    ->badge()
                    ->color(fn ($state) => $state > 0 ? 'success' : 'warning'),

                TextColumn::make('created_at')
                    ->label('投稿日時')
                    ->dateTime('Y/m/d H:i')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('reply_status')
                    ->label('返信状況')
                    ->options([
                        'replied'   => '返信済み',
                        'unreplied' => '未返信',
                    ])
                    ->query(function (Builder $query, array $data) {
                        if ($data['value'] === 'replied') {
                            $query->whereHas('activeReplies');
                        } elseif ($data['value'] === 'unreplied') {
                            $query->whereDoesntHave('activeReplies');
                        }
                    }),

                Filter::make('created_at')
                    ->label('投稿日時')
                    ->form([
                        DatePicker::make('from')->label('開始日'),
                        DatePicker::make('until')->label('終了日'),
                    ])
                    ->query(function (Builder $query, array $data) {
                        if ($data['from']) {
                            $query->whereDate('created_at', '>=', $data['from']);
                        }
                        if ($data['until']) {
                            $query->whereDate('created_at', '<=', $data['until']);
                        }
                    }),

                Filter::make('comment')
                    ->label('口コミ検索')
                    ->form([
                        TextInput::make('keyword')->label('キーワード'),
                    ])
                    ->query(function (Builder $query, array $data) {
                        if ($data['keyword']) {
                            $query->where('comment', 'like', '%' . $data['keyword'] . '%');
                        }
                    }),

                Filter::make('member')
                    ->label('投稿者検索')
                    ->form([
                        TextInput::make('nickname')->label('ニックネーム'),
                    ])
                    ->query(function (Builder $query, array $data) {
                        if ($data['nickname']) {
                            $query->whereHas('member', function ($q) use ($data) {
                                $q->where('nickname', 'like', '%' . $data['nickname'] . '%');
                            });
                        }
                    }),
            ], FiltersLayout::AboveContent)
            ->actions([
                Action::make('detail')
                    ->label('詳細')
                    ->url(fn (StkDealerReview $record) => DealerReviewDetail::getUrl(['id' => $record->id])),
            ], position: ActionsPosition::BeforeColumns);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListDealerReviews::route('/'),
        ];
    }
}