<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Constants\InquiryStatus;
use App\Constants\NavigationGroup;
use App\Constants\NavigationSort;
use App\Filament\Resources\InquiryResource\Pages\ListInquiries;
use App\Filament\Resources\InquiryResource\Pages\ViewInquiry;
use App\Infrastructure\Eloquent\User\StkInquiry;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use App\Filament\Concerns\HasResourcePermission;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Filament\Tables\Actions\Action;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class InquiryResource extends Resource
{
    use HasResourcePermission;
    protected static ?string $model            = StkInquiry::class;
    protected static ?string $navigationIcon   = 'heroicon-o-envelope';
    protected static ?string $navigationGroup  = NavigationGroup::DEALER_GROUP->value;
    protected static ?int    $navigationSort   = NavigationSort::INQUIRY_LIST->value;
    protected static ?string $pluralModelLabel = '問い合わせ管理';
    protected static ?string $modelLabel       = '問い合わせ';

    public static function canAccess(): bool
    {
        $user = Auth::user();
        return in_array($user?->role, ['super', 'admin']) || $user?->dealer_id !== null;
    }

    public static function getNavigationBadge(): ?string
    {
        return (string) static::getEloquentQuery()
            ->where('status', InquiryStatus::NEW)
            ->count() ?: null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        $user  = Auth::user();

        // ディーラーは自分の問い合わせのみ
        if (!in_array($user?->role, ['super', 'admin']) && $user?->dealer_id) {
            $query->where('dealer_id', $user->dealer_id);
        }

        return $query;
    }

    public static function form(Form $form): Form
    {
        return $form->schema([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable()
                    ->formatStateUsing(fn ($state) => "#{$state}"),

                TextColumn::make('dealer.name')
                    ->label('ディーラー')
                    ->searchable()
                    ->visible(fn () => in_array(Auth::user()?->role, ['super', 'admin'])),

                TextColumn::make('inquiry_type')
                    ->label('種別')
                    ->badge()
                    ->formatStateUsing(fn ($state) => match($state) {
                        'stock_check'     => '在庫確認',
                        'estimate'        => '見積依頼',
                        'condition_check' => '車両状態確認',
                        'other'           => 'その他',
                        default           => $state,
                    })
                    ->color(fn ($state) => match($state) {
                        'stock_check'     => 'info',
                        'estimate'        => 'warning',
                        'condition_check' => 'primary',
                        'other'           => 'gray',
                        default           => 'gray',
                    }),

                TextColumn::make('name')
                    ->label('お名前')
                    ->formatStateUsing(function ($state, $record) {
                        // ログイン会員の場合はmember_idを表示
                        if ($record->member_id && !$state) {
                            return '会員';
                        }
                        return $state ?? $record->nickname ?? '-';
                    }),

                TextColumn::make('message')
                    ->label('内容')
                    ->limit(40)
                    ->placeholder('-'),

                TextColumn::make('status')
                    ->label('ステータス')
                    ->badge()
                    ->color(fn ($state) => InquiryStatus::COLORS[$state] ?? 'gray')
                    ->formatStateUsing(fn ($state) => InquiryStatus::LABELS[$state] ?? $state),

                TextColumn::make('created_at')
                    ->label('受信日時')
                    ->dateTime('Y/m/d H:i')
                    ->sortable(),

                TextColumn::make('replied_at')
                    ->label('返信日時')
                    ->dateTime('Y/m/d H:i')
                    ->placeholder('-')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('inquiry_type')
                    ->label('種別')
                    ->options([
                        'stock_check'     => '在庫確認',
                        'estimate'        => '見積依頼',
                        'condition_check' => '車両状態確認',
                        'other'           => 'その他',
                    ]),
            ])
            ->actions([
                Action::make('view')
                    ->label('詳細')
                    ->icon('heroicon-o-eye')
                    ->url(fn (StkInquiry $record) => ViewInquiry::getUrl(['record' => $record])),
            ])
            ->recordUrl(fn (StkInquiry $record) => ViewInquiry::getUrl(['record' => $record]));
    }

    public static function getPages(): array
    {
        return [
            'index' => ListInquiries::route('/'),
            'view'  => ViewInquiry::route('/{record}'),
        ];
    }
}