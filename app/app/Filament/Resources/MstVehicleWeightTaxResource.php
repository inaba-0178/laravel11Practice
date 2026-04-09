<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Filament\Resources\MstVehicleWeightTaxResource\Pages\ListMstVehicleWeightTaxes;
use App\Infrastructure\Eloquent\Mst\MstVehicleWeightTax;
use App\Constants\NavigationSort;
use App\Constants\NavigationGroup;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Actions\Action;
use App\Filament\Pages\MstVehicleWeightTaxDetail;
use Filament\Tables\Enums\ActionsPosition;

class MstVehicleWeightTaxResource extends Resource
{
    protected static ?string $model            = MstVehicleWeightTax::class;
    protected static ?string $navigationIcon   = 'heroicon-o-scale';
    protected static ?string $navigationGroup  = NavigationGroup::MST_GROUP->value;
    protected static ?int    $navigationSort   = NavigationSort::MST_VEHICLE_WEIGHT_TAX->value;
    protected static ?string $pluralModelLabel = '自動車重量税';
    protected static ?string $modelLabel       = '自動車重量税';

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),

                TextColumn::make('weight_from')
                    ->label('重量（kg・以上）')
                    ->sortable(),

                TextColumn::make('weight_to')
                    ->label('重量（kg・未満）')
                    ->sortable(),

                IconColumn::make('is_light')
                    ->label('軽自動車')
                    ->boolean(),

                TextColumn::make('amount')
                    ->label('重量税（円）')
                    ->money('JPY')
                    ->sortable(),
            ])
            ->defaultSort('id')
            ->actions([
                Action::make('detail')
                    ->label('詳細')
                    ->action('detail')
                    ->url(function (MstVehicleWeightTax $mstVehicleWeightTax) {
                        return MstVehicleWeightTaxDetail::getUrl([
                            'id' => $mstVehicleWeightTax->id
                        ]);
                    }),
            ], position: ActionsPosition::BeforeColumns);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListMstVehicleWeightTaxes::route('/'),
        ];
    }
}