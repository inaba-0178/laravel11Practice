<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MstManufacturersResource\Pages;
use App\Infrastructure\Eloquent\Mst\MstManufacturers;
use Filament\Tables\Columns\TextColumn;
use Filament\Resources\Resource;
use App\Filament\Concerns\HasResourcePermission;
use Filament\Tables\Table;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Enums\ActionsPosition;
use Filament\Tables\Actions\Action;
use App\Filament\Pages\MstManufacturerDetail;
use App\Constants\NavigationSort;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Columns\ImageColumn;

class MstManufacturersResource extends Resource
{
    use HasResourcePermission;
    protected static ?string $model = MstManufacturers::class;

    protected static ?string    $navigationIcon     = 'heroicon-o-rectangle-stack';
    protected static ?string    $navigationGroup    = 'マスタ参照';
    protected static ?int       $navigationSort     = NavigationSort::MST_MANUFACTURER->value;
    protected static ?string    $pluralModelLabel   = 'メーカー一覧';

    public static function table(Table $table): Table
    {
        return $table
            ->searchable(false)
            ->query(
                MstManufacturers::query()->with('image')
            )
            ->columns([
                TextColumn::make('name')
                    ->label('メーカー名')
                    ->sortable(),
                TextColumn::make('display_name')
                    ->label('表示名')
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
                    ->label('メーカー')
                    ->options(
                        MstManufacturers::query()->orderBy('sort_order')->pluck('display_name', 'id')->toArray()
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
                    ->url(function (MstManufacturers $mstManufacturer) {
                        return MstManufacturerDetail::getUrl([
                            'id' => $mstManufacturer->id
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
            'index' => Pages\ListMstManufacturers::route('/'),
        ];
    }
}
