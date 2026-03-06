<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MstCarSeriesResource\Pages;
use App\Infrastructure\Eloquent\Mst\MstCarSeries;
use App\Infrastructure\Eloquent\Mst\MstBodyTypes;
use App\Infrastructure\Eloquent\Mst\MstManufacturers;
use Filament\Tables\Columns\TextColumn;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Enums\ActionsPosition;
use Filament\Tables\Actions\Action;
use App\Filament\Pages\MstCarSeriesDetail;
use App\Constants\NavigationSort;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;

class MstCarSeriesResource extends Resource
{
    protected static ?string $model = MstCarSeries::class;

    protected static ?string    $navigationIcon     = 'heroicon-o-rectangle-stack';
    protected static ?string    $navigationGroup    = 'マスタ参照';
    protected static ?int       $navigationSort     = NavigationSort::MST_CAR_SERIES->value;
    protected static ?string    $pluralModelLabel   = '車両一覧';

    public static function table(Table $table): Table
    {
        $mstCarSeries = MstCarSeries::query()
                            ->with([
                                'mstCarSeriesBodyTypes',
                                'mstCarSeriesBodyTypes.mstBodyType',
                                'mstManufacturer',
                            ]);

        return $table
            ->searchable(false)
            ->query($mstCarSeries)
            ->columns([
                TextColumn::make('series_name')
                    ->label('車両名')
                    ->sortable(),
                TextColumn::make('mstManufacturer.name')
                    ->label('メーカー名')
                    ->sortable(),
                TextColumn::make('mstCarSeriesBodyTypes.mstBodyType.name')
                    ->label('ボディタイプ名')
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('作成日時')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('series_id')
                    ->label('車両名')
                    ->searchable()
                    ->options(
                        MstCarSeries::limit(50)->pluck('series_name', 'series_id')
                    )
                    ->getSearchResultsUsing(fn (string $search) => 
                        MstCarSeries::where('series_name', 'like', "%{$search}%")
                            ->limit(50)
                            ->pluck('series_name', 'series_id')
                    )
                    ->getOptionLabelUsing(fn ($value) => 
                        MstCarSeries::find($value)?->series_name
                    ),
                SelectFilter::make('manufacturer_id')
                    ->label('メーカー名')
                    ->searchable()
                    ->options(
                        MstManufacturers::limit(50)->pluck('name', 'id')
                    )
                    ->getSearchResultsUsing(fn (string $search) => 
                        MstManufacturers::where('name', 'like', "%{$search}%")
                            ->limit(50)
                            ->pluck('name', 'id')
                    )
                    ->getOptionLabelUsing(fn ($value) => 
                        MstManufacturers::find($value)?->name
                    )
                    ->query(fn (Builder $query, array $data) => 
                        $query->when(
                            $data['value'],
                            fn ($query) => $query->where('manufacturer_id', $data['value'])
                        )
                    ),
                SelectFilter::make('body_type_id')
                    ->label('ボディタイプ名')
                    ->searchable()
                    ->options(
                        MstBodyTypes::query()->orderBy('sort_order')->pluck('name', 'id')->toArray()
                    )
                    ->query(fn (Builder $query, array $data) => 
                        $query->when(
                            $data['value'],
                            fn ($query) => $query->whereHas('mstCarSeriesBodyTypes', function ($q) use ($data) {
                                $q->where('body_type_id', $data['value']);
                            })
                        )
                    ),
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
                    ->url(function (MstCarSeries $mstCarSerie) {
                        return MstCarSeriesDetail::getUrl([
                            'series_id' => $mstCarSerie->series_id
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
            'index' => Pages\ListMstCarSeries::route('/'),
        ];
    }
}
