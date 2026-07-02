<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Filament\Resources\AffiliatedStoreResource\Pages\ListAffiliatedStores;
use App\Filament\Resources\AffiliatedStoreResource\Pages\CreateAffiliatedStore;
use App\Filament\Pages\AffiliatedStoreDetail;
use App\Infrastructure\Eloquent\User\StkAffiliatedStore;
use App\Constants\NavigationSort;
use App\Constants\NavigationGroup;
use App\Constants\RoleConstants;
use App\Constants\Role\RoleManagement;
use Filament\Resources\Resource;
use App\Filament\Concerns\HasResourcePermission;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Actions\Action;
use Filament\Tables\Enums\ActionsPosition;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder;
use App\Constants\AffiliatedStoreStatus;
use App\Constants\AffiliatedStoreType;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Forms\Components\TextInput;

class AffiliatedStoreResource extends Resource
{
    use HasResourcePermission;
    protected static ?string $model            = StkAffiliatedStore::class;
    protected static ?string $navigationIcon   = 'heroicon-o-building-storefront';
    protected static ?string $navigationGroup  = NavigationGroup::DEALER_GROUP->value;
    protected static ?int    $navigationSort   = NavigationSort::AFFILIATED_STORE->value;
    protected static ?string $pluralModelLabel = '系列店・提携店管理';
    protected static ?string $modelLabel       = '系列店・提携店';


    public static function getEloquentQuery(): Builder
    {
        $user  = Auth::user();
        $query = parent::getEloquentQuery()
            ->with(['dealer', 'affiliatedDealer'])
            ->where(function ($q) use ($user) {
                $q->where('dealer_id', $user->dealer_id)
                  ->orWhere('affiliated_dealer_id', $user->dealer_id);
            });

        return $query;
    }

    public static function table(Table $table): Table
    {
        $user = Auth::user();

        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),

                TextColumn::make('type')
                    ->label('種別')
                    ->formatStateUsing(fn ($state) => AffiliatedStoreType::label($state))
                    ->badge()
                    ->color(fn ($state) => AffiliatedStoreType::color($state)),

                TextColumn::make('status')
                    ->label('ステータス')
                    ->formatStateUsing(fn ($state) => AffiliatedStoreStatus::label($state))
                    ->badge()
                    ->color(fn ($state) => AffiliatedStoreStatus::color($state)),

                TextColumn::make('store_name')
                    ->label('店舗名')
                    ->getStateUsing(function ($record) use ($user) {
                        // 自分が申請元なら相手店舗名、申請先なら申請元店舗名を表示
                        if ($record->dealer_id === $user->dealer_id) {
                            return $record->affiliatedDealer?->name ?? '-';
                        }
                        return $record->dealer?->name ?? '-';
                    }),

                TextColumn::make('store_address')
                    ->label('住所')
                    ->getStateUsing(function ($record) use ($user) {
                        $target = $record->dealer_id === $user->dealer_id
                            ? $record->affiliatedDealer
                            : $record->dealer;
                        return $target
                            ? ($target->city ?? '') . ($target->address_detail ?? '')
                            : '-';
                    }),

                TextColumn::make('store_phone')
                    ->label('電話番号')
                    ->getStateUsing(function ($record) use ($user) {
                        $target = $record->dealer_id === $user->dealer_id
                            ? $record->affiliatedDealer
                            : $record->dealer;
                        return $target?->phone ?? '-';
                    }),

                TextColumn::make('requested_at')
                    ->label('申請日時')
                    ->dateTime('Y/m/d H:i')
                    ->sortable(),
            ])
            ->defaultSort('requested_at', 'desc')
            ->filters([
                SelectFilter::make('type')
                    ->label('種別')
                    ->options([
                        AffiliatedStoreType::AFFILIATED => AffiliatedStoreType::label(AffiliatedStoreType::AFFILIATED),
                        AffiliatedStoreType::PARTNER    => AffiliatedStoreType::label(AffiliatedStoreType::PARTNER),
                    ]),

                SelectFilter::make('status')
                    ->label('ステータス')
                    ->options([
                        AffiliatedStoreStatus::PENDING   => AffiliatedStoreStatus::label(AffiliatedStoreStatus::PENDING),
                        AffiliatedStoreStatus::APPROVED  => AffiliatedStoreStatus::label(AffiliatedStoreStatus::APPROVED),
                        AffiliatedStoreStatus::REJECTED  => AffiliatedStoreStatus::label(AffiliatedStoreStatus::REJECTED),
                        AffiliatedStoreStatus::DISSOLVED => AffiliatedStoreStatus::label(AffiliatedStoreStatus::DISSOLVED),
                    ]),

                Filter::make('store_name')
                    ->label('店舗名')
                    ->form([
                        TextInput::make('name')->label('店舗名'),
                    ])
                    ->query(function (Builder $query, array $data) {
                        if ($data['name']) {
                            $user = Auth::user();
                            $query->where(function ($q) use ($data, $user) {
                                $q->whereHas('dealer', fn ($q) => $q->where('name', 'like', '%' . $data['name'] . '%'))
                                ->orWhereHas('affiliatedDealer', fn ($q) => $q->where('name', 'like', '%' . $data['name'] . '%'));
                            });
                        }
                    }),
            ], layout: FiltersLayout::AboveContent)
            ->filtersFormColumns(3)         // 横並び
            ->deferFilters()                // 適用ボタンを表示
            ->filtersApplyAction(
                fn (Action $action) => $action->label('適用する')
            )
            ->hiddenFilterIndicators()
            ->actions([
                Action::make('detail')
                    ->label('詳細')
                    ->url(fn (StkAffiliatedStore $record) => AffiliatedStoreDetail::getUrl(['id' => $record->id])),
            ], position: ActionsPosition::BeforeColumns);
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListAffiliatedStores::route('/'),
            'create' => CreateAffiliatedStore::route('/create'),
        ];
    }
}