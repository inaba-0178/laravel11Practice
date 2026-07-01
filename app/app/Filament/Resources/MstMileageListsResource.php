<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MstMileageListsResource\Pages;
use App\Infrastructure\Eloquent\Mst\MstMileageLists;
use Filament\Tables\Columns\TextColumn;
use Filament\Resources\Resource;
use App\Filament\Concerns\HasResourcePermission;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Enums\ActionsPosition;
use Filament\Tables\Actions\Action;
use App\Filament\Pages\MstMileageListDetail;
use App\Constants\NavigationSort;
use Filament\Tables\Filters\SelectFilter;
use App\Domain\Common\Enums\MileageList;

class MstMileageListsResource extends Resource
{
    use HasResourcePermission;
    protected static ?string $model = MstMileageLists::class;

    protected static ?string    $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string    $navigationGroup = 'マスタ参照';
    protected static ?int       $navigationSort = NavigationSort::MST_MILEAGE_LIST->value;
    protected static ?string    $pluralModelLabel = '走行距離一覧';

    public static function table(Table $table): Table
    {
        return $table
            ->searchable(false)
            ->columns([
                TextColumn::make('name')
                    ->label('走行距離')
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('作成日時')
                    ->sortable(),
            ])
            ->filters([
                selectFilter::make('name')
                    ->label('走行距離')
                    ->options(MileageList::labels()->toArray())
                    ->query(function (Builder $query, array $data) {
                        if (blank($data['value'])) {
                            return $query;
                        }
                        return MileageList::from((int) $data['value'])->applyQuery($query);
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
                    ->url(function (MstMileageLists $mstMileage) {
                        return MstMileageListDetail::getUrl([
                            'id' => $mstMileage->id
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
            'index' => Pages\ListMstMileageLists::route('/'),
        ];
    }
}
