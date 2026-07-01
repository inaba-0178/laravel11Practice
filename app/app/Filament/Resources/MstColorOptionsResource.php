<?php

namespace App\Filament\Resources;

use App\Constants\NavigationGroup;
use App\Constants\NavigationSort;
use App\Filament\Pages\MstColorOptionsDetail;
use App\Filament\Resources\MstColorOptionsResource\Pages;
use App\Infrastructure\Eloquent\Mst\MstColorOptions;
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

class MstColorOptionsResource extends Resource
{
    use HasResourcePermission;
    protected static ?string $model = MstColorOptions::class;

    protected static ?string $navigationIcon   = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup  = NavigationGroup::MST_GROUP->value;
    protected static ?int    $navigationSort   = NavigationSort::MST_COLOR_OPTIONS->value;
    protected static ?string $pluralModelLabel = 'カラーオプション一覧';

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
                TextColumn::make('hex_code')
                    ->label('カラーコード')
                    ->html()
                    ->formatStateUsing(fn(?string $state) => $state
                        ? "<span style='display:inline-flex;align-items:center;gap:6px;'><span style='display:inline-block;width:20px;height:20px;border-radius:50%;background:{$state};border:1px solid #ccc;'></span>{$state}</span>"
                        : '-')
                    ->sortable(),
                TextColumn::make('group')
                    ->label('色系統')
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
                Filter::make('group')
                    ->form([TextInput::make('group')->label('色系統')])
                    ->query(fn(Builder $query, array $data) => blank($data['group'])
                        ? $query
                        : $query->where('group', 'like', "%{$data['group']}%")),
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
                    ->url(fn(MstColorOptions $record) => MstColorOptionsDetail::getUrl(['id' => $record->id])),
            ], position: ActionsPosition::BeforeColumns);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMstColorOptions::route('/'),
        ];
    }
}
