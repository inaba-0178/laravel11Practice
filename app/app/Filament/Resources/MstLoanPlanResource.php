<?php

namespace App\Filament\Resources;

use App\Constants\NavigationGroup;
use App\Constants\NavigationSort;
use App\Filament\Pages\MstLoanPlanDetail;
use App\Filament\Resources\MstLoanPlanResource\Pages;
use App\Infrastructure\Eloquent\Mst\MstLoanPlan;
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

class MstLoanPlanResource extends Resource
{
    protected static ?string $model = MstLoanPlan::class;

    protected static ?string $navigationIcon   = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationGroup  = NavigationGroup::MST_GROUP->value;
    protected static ?int    $navigationSort   = NavigationSort::MST_LOAN_PLAN->value;
    protected static ?string $pluralModelLabel = 'ローンプラン一覧';

    public static function table(Table $table): Table
    {
        return $table
            ->searchable(false)
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),
                TextColumn::make('name')
                    ->label('プラン名')
                    ->sortable(),
                TextColumn::make('interest_rate')
                    ->label('金利（%）')
                    ->sortable(),
                TextColumn::make('min_months')
                    ->label('最小回数')
                    ->sortable(),
                TextColumn::make('max_months')
                    ->label('最大回数')
                    ->sortable(),
                IconColumn::make('is_default')
                    ->label('デフォルト')
                    ->boolean()
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
                Filter::make('name')
                    ->form([TextInput::make('name')->label('プラン名')])
                    ->query(fn(Builder $query, array $data) => blank($data['name'])
                        ? $query
                        : $query->where('name', 'like', "%{$data['name']}%")),
                SelectFilter::make('is_default')
                    ->label('デフォルト')
                    ->options(['1' => 'デフォルト', '0' => '非デフォルト']),
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
                    ->url(fn(MstLoanPlan $record) => MstLoanPlanDetail::getUrl(['id' => $record->id])),
            ], position: ActionsPosition::BeforeColumns);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMstLoanPlans::route('/'),
        ];
    }
}
