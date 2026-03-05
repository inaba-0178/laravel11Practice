<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MstBodyTypesResource\Pages;
use App\Infrastructure\Eloquent\Mst\MstBodyTypes;
use Filament\Tables\Columns\TextColumn;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Enums\ActionsPosition;
use Filament\Tables\Actions\Action;
use App\Filament\Pages\MstBodyTypeDetail;
use App\Constants\NavigationSort;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Columns\ImageColumn;

class MstBodyTypesResource extends Resource
{
    protected static ?string $model = MstBodyTypes::class;

    protected static ?string    $navigationIcon     = 'heroicon-o-rectangle-stack';
    protected static ?string    $navigationGroup    = 'マスタ参照';
    protected static ?int       $navigationSort     = NavigationSort::MST_BODY_TYPE->value;
    protected static ?string    $pluralModelLabel   = 'ボディタイプ一覧';

    public static function table(Table $table): Table
    {
        return $table
            ->searchable(false)
            ->query(
                MstBodyTypes::query()->with('image')
            )
            ->columns([
                TextColumn::make('name')
                    ->label('ボディタイプ名')
                    ->sortable(),
                ImageColumn::make('image.file_path')
                    ->label('画像')
                    ->disk('public'),
                TextColumn::make('sort_order')
                    ->label('表示順')
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('作成日時')
                    ->sortable(),
            ])
            ->filters([
                    selectFilter::make('id')
                    ->label('ボディタイプ名')
                    ->options(
                        MstBodyTypes::query()->orderBy('sort_order')->pluck('name', 'id')->toArray()
                ),
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
                    ->action('detail')
                    ->url(function (MstBodyTypes $mstBodyType) {
                        return MstBodyTypeDetail::getUrl([
                            'id' => $mstBodyType->id
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
            'index' => Pages\ListMstBodyTypes::route('/'),
        ];
    }
}
