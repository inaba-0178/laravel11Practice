<?php

namespace App\Filament\Resources;

use App\Constants\NavigationSort;
use App\Filament\Resources\MstVehicleTaxResource\Pages;
use App\Infrastructure\Eloquent\Mst\MstVehicleTax;
use Filament\Resources\Resource;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\ActionsPosition;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class MstVehicleTaxResource extends Resource
{
    protected static ?string $model = MstVehicleTax::class;

    protected static ?string $navigationIcon   = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup  = 'マスタ参照';
    protected static ?int    $navigationSort   = NavigationSort::MST_VEHICLE_TAX->value;
    protected static ?string $pluralModelLabel = '自動車税一覧';

    public static function table(Table $table): Table
    {
        return $table
            ->searchable(false)
            ->query(MstVehicleTax::query()->with('displacementList'))
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),
                TextColumn::make('displacementList.name')
                    ->label('排気量区分')
                    ->sortable(),
                TextColumn::make('displacementList.min_amount')
                    ->label('排気量（下限）')
                    ->sortable(),
                TextColumn::make('displacementList.max_amount')
                    ->label('排気量（上限）'),
                IconColumn::make('is_light')
                    ->label('軽自動車')
                    ->boolean()
                    ->sortable(),
                TextColumn::make('amount')
                    ->label('税額')
                    ->money('JPY', locale: 'ja')
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('作成日時')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('is_light')
                    ->label('軽自動車')
                    ->options([
                        '1' => '軽自動車',
                        '0' => '普通車',
                    ]),
            ], FiltersLayout::AboveContent)
            ->deferFilters()
            ->hiddenFilterIndicators()
            ->filtersApplyAction(fn(Action $action) => $action->label('適用'));
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMstVehicleTaxes::route('/'),
        ];
    }
}
