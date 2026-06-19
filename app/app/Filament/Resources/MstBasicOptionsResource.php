<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MstBasicOptionResource\Pages;
use App\Infrastructure\Eloquent\Mst\MstBasicOptions;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Enums\ActionsPosition;
use Filament\Tables\Actions\Action;
use App\Filament\Pages\MstBasicOptionDetail;
use App\Constants\NavigationSort;
use App\Constants\NavigationGroup;

class MstBasicOptionsResource extends Resource
{
    protected static ?string $model = MstBasicOptions::class;

    protected static ?string $navigationIcon  = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup = NavigationGroup::MST_GROUP->value;
    protected static ?int    $navigationSort  = NavigationSort::MST_BASIC_OPTION->value;
    protected static ?string $pluralModelLabel = '基本オプション一覧';

    public static function table(Table $table): Table
    {
        return $table
            ->searchable(false)
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),
                TextColumn::make('label')
                    ->label('ラベル'),
                TextColumn::make('value')
                    ->label('値'),
                TextColumn::make('sort_order')
                    ->label('表示順')
                    ->sortable(),
                IconColumn::make('is_highlight')
                    ->label('ハイライト')
                    ->boolean(),
                IconColumn::make('is_active')
                    ->label('利用可否')
                    ->boolean(),
                TextColumn::make('created_at')
                    ->label('作成日時')
                    ->sortable(),
            ])
            ->filters([
                Filter::make('id')
                    ->form([
                        TextInput::make('id')
                            ->label('ID'),
                    ])
                    ->query(function (Builder $query, array $data) {
                        if (blank($data['id'])) {
                            return $query;
                        }
                        return $query->where('id', 'like', "%{$data['id']}%");
                    }),
                Filter::make('label')
                    ->form([
                        TextInput::make('label')
                            ->label('ラベル'),
                    ])
                    ->query(function (Builder $query, array $data) {
                        if (blank($data['label'])) {
                            return $query;
                        }
                        return $query->where('label', 'like', "%{$data['label']}%");
                    }),
                Filter::make('value')
                    ->form([
                        TextInput::make('value')
                            ->label('値'),
                    ])
                    ->query(function (Builder $query, array $data) {
                        if (blank($data['value'])) {
                            return $query;
                        }
                        return $query->where('value', 'like', "%{$data['value']}%");
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
                    ->url(function (MstBasicOptions $record) {
                        return MstBasicOptionDetail::getUrl([
                            'id' => $record->id,
                        ]);
                    }),
            ], position: ActionsPosition::BeforeColumns);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMstBasicOptions::route('/'),
        ];
    }
}
