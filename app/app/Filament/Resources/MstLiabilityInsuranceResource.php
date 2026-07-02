<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Filament\Resources\MstLiabilityInsuranceResource\Pages\ListMstLiabilityInsurances;
use App\Infrastructure\Eloquent\Mst\MstLiabilityInsurance;
use App\Constants\NavigationSort;
use App\Constants\NavigationGroup;
use Filament\Resources\Resource;
use App\Filament\Concerns\HasResourcePermission;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Actions\Action;
use App\Filament\Pages\MstLiabilityInsuranceDetail;
use Filament\Tables\Enums\ActionsPosition;

class MstLiabilityInsuranceResource extends Resource
{
    use HasResourcePermission;
    protected static ?string $model            = MstLiabilityInsurance::class;
    protected static ?string $navigationIcon   = 'heroicon-o-shield-check';
    protected static ?string $navigationGroup  = NavigationGroup::MST_GROUP->value;
    protected static ?int    $navigationSort   = NavigationSort::MST_LIABILITY_INSURANCE->value;
    protected static ?string $pluralModelLabel = '自賠責保険料';
    protected static ?string $modelLabel       = '自賠責保険料';

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),

                TextColumn::make('vehicle_type')
                    ->label('車種区分')
                    ->formatStateUsing(fn (string $state) => $state === 'light' ? '軽自動車' : '普通車')
                    ->sortable(),

                TextColumn::make('months')
                    ->label('保険期間')
                    ->formatStateUsing(fn (int $state) => $state . 'ヶ月')
                    ->sortable(),

                TextColumn::make('amount')
                    ->label('保険料（円）')
                    ->money('JPY')
                    ->sortable(),
            ])
            ->defaultSort('vehicle_type')
            ->actions([
                Action::make('detail')
                    ->label('詳細')
                    ->url(fn (MstLiabilityInsurance $record) => MstLiabilityInsuranceDetail::getUrl(['id' => $record->id])),
            ], position: ActionsPosition::BeforeColumns);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListMstLiabilityInsurances::route('/'),
        ];
    }
}