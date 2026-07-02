<?php

namespace App\Filament\Resources;

use App\Constants\NavigationGroup;
use App\Constants\NavigationSort;
use App\Filament\Pages\MstDisplacementListDetail;
use App\Filament\Resources\MstDisplacementListsResource\Pages;
use App\Infrastructure\Eloquent\Mst\MstDisplacementLists;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use App\Filament\Concerns\HasResourcePermission;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\ActionsPosition;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class MstDisplacementListsResource extends Resource
{
    use HasResourcePermission;
    protected static ?string $model = MstDisplacementLists::class;

    protected static ?string $navigationIcon   = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup  = NavigationGroup::MST_GROUP->value;
    protected static ?int    $navigationSort   = NavigationSort::MST_DISPLACEMENT_LIST->value;
    protected static ?string $pluralModelLabel = '排気量一覧';

    public static function table(Table $table): Table
    {
        return $table
            ->searchable(false)
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),
                TextColumn::make('name')
                    ->label('排気量名')
                    ->sortable(),
                TextColumn::make('min_amount')
                    ->label('最小排気量')
                    ->sortable(),
                TextColumn::make('max_amount')
                    ->label('最大排気量')
                    ->sortable(),
                IconColumn::make('is_unlimited')
                    ->label('上限なし')
                    ->boolean()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('作成日時')
                    ->sortable(),
            ])
            ->filters([
                Filter::make('name')
                    ->form([TextInput::make('name')->label('排気量名')])
                    ->query(fn(Builder $query, array $data) => blank($data['name'])
                        ? $query
                        : $query->where('name', 'like', "%{$data['name']}%")),
                Filter::make('min_amount_from')
                    ->form([TextInput::make('min_amount_from')->label('最小排気量以上')->numeric()])
                    ->query(fn(Builder $query, array $data) => blank($data['min_amount_from'])
                        ? $query
                        : $query->where('min_amount', '>=', $data['min_amount_from'])),
                Filter::make('min_amount_to')
                    ->form([TextInput::make('min_amount_to')->label('最小排気量以下')->numeric()])
                    ->query(fn(Builder $query, array $data) => blank($data['min_amount_to'])
                        ? $query
                        : $query->where('min_amount', '<=', $data['min_amount_to'])),
                Filter::make('max_amount_from')
                    ->form([TextInput::make('max_amount_from')->label('最大排気量以上')->numeric()])
                    ->query(fn(Builder $query, array $data) => blank($data['max_amount_from'])
                        ? $query
                        : $query->where('max_amount', '>=', $data['max_amount_from'])),
                Filter::make('max_amount_to')
                    ->form([TextInput::make('max_amount_to')->label('最大排気量以下')->numeric()])
                    ->query(fn(Builder $query, array $data) => blank($data['max_amount_to'])
                        ? $query
                        : $query->where('max_amount', '<=', $data['max_amount_to'])),
                SelectFilter::make('is_unlimited')
                    ->label('上限なし')
                    ->options(['1' => '上限なし', '0' => '上限あり']),
            ], FiltersLayout::AboveContent)
            ->deferFilters()
            ->hiddenFilterIndicators()
            ->filtersApplyAction(fn(Action $action) => $action->label('適用'))
            ->actions([
                Action::make('detail')
                    ->label('詳細')
                    ->url(fn(MstDisplacementLists $record) => MstDisplacementListDetail::getUrl(['id' => $record->id])),
            ], position: ActionsPosition::BeforeColumns);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMstDisplacementLists::route('/'),
        ];
    }
}
