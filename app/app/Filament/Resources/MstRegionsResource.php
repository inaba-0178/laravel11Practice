<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MstRegionsResource\Pages;
use App\Infrastructure\Eloquent\Mst\MstRegions;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\TextColumn;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Enums\ActionsPosition;
use Filament\Tables\Actions\Action;
use App\Filament\Pages\MstRegionDetail;
use App\Constants\NavigationSort;
use App\Domain\Common\Enums\AreaCode;
use Filament\Tables\Filters\SelectFilter;

class MstRegionsResource extends Resource
{
    protected static ?string $model = MstRegions::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = 'マスタ参照';
    protected static ?int $navigationSort = NavigationSort::MST_REGION->value;
    protected static ?string $pluralModelLabel = '都道府県一覧';

    public static function table(Table $table): Table
    {
        return $table
            ->searchable(false)
            ->columns([
                TextColumn::make('name')
                    ->label('地方名'),
                TextColumn::make('area_code')
                    ->label('エリアコード')
                    ->getStateUsing(function ($record) {
                        return AreaCode::tryFrom($record->area_code)?->label() ?? '';
                    })
                    ->sortable(),
                TextColumn::make('sort_order')
                    ->label('表示順')
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('作成日時')
                    ->sortable(),
            ])
            ->filters([
                Filter::make('id')
                    ->form([
                        TextInput::make('id')
                            ->label('ID')
                    ])
                    ->query(function (Builder $query, array $data) {
                        if (blank($data['id'])) {
                            return $query;
                        }
                        return $query->where('id', 'like', "%{$data['id']}%");
                    }),
                Filter::make('name')
                    ->form([
                        TextInput::make('name')
                            ->label('名称')
                    ])
                    ->query(function (Builder $query, array $data) {
                        if (blank($data['name'])) {
                            return $query;
                        }
                        return $query->where('name', 'like', "%{$data['name']}%");
                    }),
                selectFilter::make('area_code')
                    ->options(AreaCode::labels()->toArray())
                    ->query(function (Builder $query, array $data) {
                        if (blank($data['value'])) {
                            return $query;
                        }
                        return $query->where('area_code',$data['value']);
                    }),
            ], FiltersLayout::AboveContent)
            ->deferFilters()
            ->hiddenFilterIndicators()
            ->filtersApplyAction(
                fn(Action $action) => $action
                    ->label('適用')
            )
            ->Actions([
                Action::make('detail')
                    ->label('詳細')
                    ->action('detail')
                    ->url(function (MstRegions $mstArea) {
                        return MstRegionDetail::getUrl([
                            'id' => $mstArea->id
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
            'index' => Pages\ListMstRegions::route('/'),
        ];
    }
}
