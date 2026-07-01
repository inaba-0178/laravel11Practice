<?php

namespace App\Filament\Resources;

use App\Constants\NavigationGroup;
use App\Constants\NavigationSort;
use App\Filament\Pages\MstFeaturedBodyTypeDetail;
use App\Filament\Resources\MstFeaturedBodyTypesResource\Pages;
use App\Infrastructure\Eloquent\Mst\MstFeaturedBodyTypes;
use App\Constants\FeaturedBodyTypePosition;
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

class MstFeaturedBodyTypesResource extends Resource
{
    use HasResourcePermission;
    protected static ?string $model = MstFeaturedBodyTypes::class;

    protected static ?string $navigationIcon   = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup  = NavigationGroup::MST_GROUP->value;
    protected static ?int    $navigationSort   = NavigationSort::MST_FEATURED_BODY_TYPE->value;
    protected static ?string $pluralModelLabel = '特集ボディタイプ一覧';

    public static function table(Table $table): Table
    {
        return $table
            ->searchable(false)
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),
                TextColumn::make('body_type_code')
                    ->label('ボディタイプコード')
                    ->sortable(),
                TextColumn::make('position')
                    ->label('表示位置')
                    ->formatStateUsing(fn(string $state) => FeaturedBodyTypePosition::LABELS[$state] ?? $state)
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
                Filter::make('body_type_code')
                    ->form([TextInput::make('body_type_code')->label('ボディタイプコード')])
                    ->query(fn(Builder $query, array $data) => blank($data['body_type_code'])
                        ? $query
                        : $query->where('body_type_code', 'like', "%{$data['body_type_code']}%")),
                SelectFilter::make('position')
                    ->label('表示位置')
                    ->options(FeaturedBodyTypePosition::LABELS),
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
                    ->url(fn(MstFeaturedBodyTypes $record) => MstFeaturedBodyTypeDetail::getUrl(['id' => $record->id])),
            ], position: ActionsPosition::BeforeColumns);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMstFeaturedBodyTypes::route('/'),
        ];
    }
}
