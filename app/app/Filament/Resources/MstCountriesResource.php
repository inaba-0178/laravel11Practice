<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MstCountryResource\Pages;
use App\Infrastructure\Eloquent\Mst\MstCountries;
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
use App\Filament\Pages\MstCountryDetail;
use App\Constants\NavigationSort;
use App\Constants\NavigationGroup;

class MstCountriesResource extends Resource
{
    protected static ?string $model = MstCountries::class;

    protected static ?string $navigationIcon  = 'heroicon-o-globe-alt';
    protected static ?string $navigationGroup = NavigationGroup::MST_GROUP->value;
    protected static ?int    $navigationSort  = NavigationSort::MST_COUNTRY->value;
    protected static ?string $pluralModelLabel = '国一覧';

    public static function table(Table $table): Table
    {
        return $table
            ->searchable(false)
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),
                TextColumn::make('country_code')
                    ->label('国コード'),
                TextColumn::make('label')
                    ->label('国名'),
                TextColumn::make('flag')
                    ->label('国旗'),
                TextColumn::make('anchor')
                    ->label('アンカー'),
                TextColumn::make('sort_order')
                    ->label('表示順')
                    ->sortable(),
                IconColumn::make('is_active')
                    ->label('利用可否')
                    ->boolean(),
                TextColumn::make('created_at')
                    ->label('作成日時')
                    ->sortable(),
            ])
            ->filters([
                Filter::make('country_code')
                    ->form([
                        TextInput::make('country_code')
                            ->label('国コード'),
                    ])
                    ->query(function (Builder $query, array $data) {
                        if (blank($data['country_code'])) {
                            return $query;
                        }
                        return $query->where('country_code', 'like', "%{$data['country_code']}%");
                    }),
                Filter::make('label')
                    ->form([
                        TextInput::make('label')
                            ->label('国名'),
                    ])
                    ->query(function (Builder $query, array $data) {
                        if (blank($data['label'])) {
                            return $query;
                        }
                        return $query->where('label', 'like', "%{$data['label']}%");
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
                    ->url(function (MstCountries $record) {
                        return MstCountryDetail::getUrl([
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
            'index' => Pages\ListMstCountries::route('/'),
        ];
    }
}
