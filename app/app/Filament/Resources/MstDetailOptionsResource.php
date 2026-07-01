<?php

namespace App\Filament\Resources;

use App\Constants\NavigationGroup;
use App\Constants\NavigationSort;
use App\Filament\Pages\MstDetailOptionsDetail;
use App\Filament\Resources\MstDetailOptionsResource\Pages;
use App\Infrastructure\Eloquent\Mst\MstDetailOptions;
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

class MstDetailOptionsResource extends Resource
{
    use HasResourcePermission;
    protected static ?string $model = MstDetailOptions::class;

    protected static ?string $navigationIcon   = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup  = NavigationGroup::MST_GROUP->value;
    protected static ?int    $navigationSort   = NavigationSort::MST_DETAIL_OPTIONS->value;
    protected static ?string $pluralModelLabel = '詳細オプション一覧';

    public static function table(Table $table): Table
    {
        return $table
            ->searchable(false)
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),
                TextColumn::make('value')
                    ->label('値（APIキー）')
                    ->sortable(),
                TextColumn::make('label')
                    ->label('表示名')
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
                Filter::make('label')
                    ->form([TextInput::make('label')->label('表示名')])
                    ->query(fn(Builder $query, array $data) => blank($data['label'])
                        ? $query
                        : $query->where('label', 'like', "%{$data['label']}%")),
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
                    ->url(fn(MstDetailOptions $record) => MstDetailOptionsDetail::getUrl(['id' => $record->id])),
            ], position: ActionsPosition::BeforeColumns);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMstDetailOptions::route('/'),
        ];
    }
}
