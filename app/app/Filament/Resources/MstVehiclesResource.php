<?php

namespace App\Filament\Resources;

use App\Constants\NavigationSort;
use App\Domain\Common\Enums\ProductionStatus;
use App\Filament\Pages\MstVehicleDetail;
use App\Filament\Resources\MstVehiclesResource\Pages;
use App\Infrastructure\Eloquent\Mst\MstVehicles;
use Filament\Resources\Resource;
use App\Filament\Concerns\HasResourcePermission;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\ActionsPosition;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class MstVehiclesResource extends Resource
{
    use HasResourcePermission;
    protected static ?string $model = MstVehicles::class;

    protected static ?string $navigationIcon   = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup  = 'マスタ参照';
    protected static ?int    $navigationSort   = NavigationSort::MST_VEHICLE->value;
    protected static ?string $pluralModelLabel = '車両マスタ一覧';
    // 注文あれば表示: protected static bool $shouldRegisterNavigation = true;
    protected static bool    $shouldRegisterNavigation = false;

    public static function table(Table $table): Table
    {
        return $table
            ->searchable(false)
            ->query(MstVehicles::query()->with(['manufacturer', 'carSeries', 'bodyType']))
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),
                TextColumn::make('name')
                    ->label('車両名')
                    ->sortable(),
                TextColumn::make('manufacturer.name')
                    ->label('メーカー名')
                    ->sortable(),
                TextColumn::make('carSeries.series_name')
                    ->label('車種シリーズ')
                    ->sortable(),
                TextColumn::make('model_code')
                    ->label('モデルコード')
                    ->sortable(),
                TextColumn::make('bodyType.name')
                    ->label('ボディタイプ')
                    ->sortable(),
                TextColumn::make('status')
                    ->label('ステータス')
                    ->formatStateUsing(fn ($state) => $state ? (ProductionStatus::tryFrom($state)?->label() ?? $state) : null)
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('作成日時')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('ステータス')
                    ->options(
                        MstVehicles::query()
                            ->distinct()
                            ->pluck('status', 'status')
                            ->filter()
                            ->toArray()
                    ),
            ], FiltersLayout::AboveContent)
            ->deferFilters()
            ->hiddenFilterIndicators()
            ->filtersApplyAction(fn(Action $action) => $action->label('適用'))
            ->actions([
                Action::make('detail')
                    ->label('詳細')
                    ->url(fn(MstVehicles $record) => MstVehicleDetail::getUrl(['id' => $record->id])),
            ], position: ActionsPosition::BeforeColumns);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMstVehicles::route('/'),
        ];
    }
}
