<?php

namespace App\Filament\Resources;

use App\Constants\NavigationGroup;
use App\Constants\NavigationSort;
use App\Filament\Pages\MstFeaturedBrandDetail;
use App\Filament\Resources\MstFeaturedBrandsResource\Pages;
use App\Infrastructure\Eloquent\Mst\MstFeaturedBrands;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\ActionsPosition;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class MstFeaturedBrandsResource extends Resource
{
    protected static ?string $model = MstFeaturedBrands::class;

    protected static ?string $navigationIcon   = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup  = NavigationGroup::MST_GROUP->value;
    protected static ?int    $navigationSort   = NavigationSort::MST_FEATURED_BRAND->value;
    protected static ?string $pluralModelLabel = '特集ブランド一覧';

    public static function table(Table $table): Table
    {
        return $table
            ->searchable(false)
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),
                TextColumn::make('manufacturer_code')
                    ->label('メーカーコード')
                    ->sortable(),
                TextColumn::make('position')
                    ->label('表示位置')
                    ->formatStateUsing(fn(string $state) => match ($state) {
                        'jp-top-row'     => '中古車',
                        'import-top-row' => '輸入中古車',
                        default          => $state,
                    })
                    ->sortable(),
                TextColumn::make('sort_order')
                    ->label('表示順')
                    ->sortable(),
                IconColumn::make('is_active')
                    ->label('有効')
                    ->boolean()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('作成日時')
                    ->sortable(),
            ])
            ->filters([
                Filter::make('manufacturer_code')
                    ->form([TextInput::make('manufacturer_code')->label('メーカーコード')])
                    ->query(fn(Builder $query, array $data) => blank($data['manufacturer_code'])
                        ? $query
                        : $query->where('manufacturer_code', 'like', "%{$data['manufacturer_code']}%")),
                SelectFilter::make('position')
                    ->label('表示位置')
                    ->options([
                        'jp-top-row'     => '中古車',
                        'import-top-row' => '輸入中古車',
                    ]),
                SelectFilter::make('is_active')
                    ->label('有効')
                    ->options(['1' => '有効', '0' => '無効']),
            ], FiltersLayout::AboveContent)
            ->deferFilters()
            ->hiddenFilterIndicators()
            ->filtersApplyAction(fn(Action $action) => $action->label('適用'))
            ->actions([
                Action::make('detail')
                    ->label('詳細')
                    ->url(fn(MstFeaturedBrands $record) => MstFeaturedBrandDetail::getUrl(['id' => $record->id])),
            ], position: ActionsPosition::BeforeColumns);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMstFeaturedBrands::route('/'),
        ];
    }
}
