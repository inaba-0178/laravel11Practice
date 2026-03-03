<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MstRidingCapacityListsResource\Pages;
use App\Infrastructure\Eloquent\Mst\MstRidingCapacityLists;
use Filament\Tables\Columns\TextColumn;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Enums\ActionsPosition;
use Filament\Tables\Actions\Action;
use App\Filament\Pages\MstRidingCapacityListDetail;
use App\Constants\NavigationSort;
use Filament\Tables\Filters\SelectFilter;
use App\Domain\Common\Enums\RidingCapacityList;

class MstRidingCapacityListsResource extends Resource
{
    protected static ?string $model = MstRidingCapacityLists::class;

    protected static ?string    $navigationIcon     = 'heroicon-o-rectangle-stack';
    protected static ?string    $navigationGroup    = 'マスタ参照';
    protected static ?int       $navigationSort     = NavigationSort::MST_RIDING_CAPACITY_LIST->value;
    protected static ?string    $pluralModelLabel   = '乗車定員一覧';

    public static function table(Table $table): Table
    {
        return $table
            ->searchable(false)
            ->columns([
                TextColumn::make('name')
                    ->label('乗車定員')
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('作成日時')
                    ->sortable(),
            ])
            ->filters([
                selectFilter::make('name')
                    ->label('乗車定員')
                    ->options(RidingCapacityList::labels()->toArray())
                    ->query(function (Builder $query, array $data) {
                        if (blank($data['value'])) {
                            return $query;
                        }
                        return $query->where('id',$data['value']);
                    }),
            ], FiltersLayout::AboveContent)
            ->deferFilters()
            ->hiddenFilterIndicators()
            ->filtersApplyAction(
                fn(Action $action) => $action
                    ->label('適用')
            )
            ->actions([
                Action::make('detail')
                    ->label('詳細')
                    ->action('detail')
                    ->url(function (MstRidingCapacityLists $mstRidingCapacity) {
                        return MstRidingCapacityListDetail::getUrl([
                            'id' => $mstRidingCapacity->id
                        ]);
                    }),
            ], position: ActionsPosition::BeforeColumns);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMstRidingCapacityLists::route('/'),
        ];
    }
}
