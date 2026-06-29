<?php

namespace App\Filament\Resources;

use App\Constants\NavigationGroup;
use App\Constants\NavigationSort;
use App\Filament\Pages\MstSeatOptionDetail;
use App\Filament\Resources\MstSeatOptionResource\Pages;
use App\Infrastructure\Eloquent\Mst\MstSeatOption;
use Filament\Resources\Resource;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\ActionsPosition;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class MstSeatOptionResource extends Resource
{
    protected static ?string $model = MstSeatOption::class;

    protected static ?string $navigationIcon   = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup  = NavigationGroup::MST_GROUP->value;
    protected static ?int    $navigationSort   = NavigationSort::MST_SEAT_OPTION->value;
    protected static ?string $pluralModelLabel = 'シートオプション一覧';

    public static function table(Table $table): Table
    {
        return $table
            ->searchable(false)
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),
                TextColumn::make('label')
                    ->label('ラベル')
                    ->sortable(),
                TextColumn::make('value')
                    ->label('値')
                    ->sortable(),
                TextColumn::make('sort_order')
                    ->label('表示順')
                    ->sortable(),
                IconColumn::make('is_active')
                    ->label('利用可否')
                    ->boolean()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('作成日時')
                    ->sortable(),
            ])
            ->filters([
                Filter::make('label')
                    ->form([TextInput::make('label')->label('ラベル')])
                    ->query(fn(Builder $query, array $data) => blank($data['label'])
                        ? $query
                        : $query->where('label', 'like', "%{$data['label']}%")),
                Filter::make('value')
                    ->form([TextInput::make('value')->label('値')])
                    ->query(fn(Builder $query, array $data) => blank($data['value'])
                        ? $query
                        : $query->where('value', 'like', "%{$data['value']}%")),
                SelectFilter::make('is_active')
                    ->label('利用可否')
                    ->options([
                        '1' => '利用可能',
                        '0' => '利用不可',
                    ]),
            ], FiltersLayout::AboveContent)
            ->deferFilters()
            ->hiddenFilterIndicators()
            ->filtersApplyAction(fn(Action $action) => $action->label('適用'))
            ->actions([
                Action::make('detail')
                    ->label('詳細')
                    ->url(fn(MstSeatOption $record) => MstSeatOptionDetail::getUrl(['id' => $record->id])),
            ], position: ActionsPosition::BeforeColumns);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMstSeatOptions::route('/'),
        ];
    }
}
