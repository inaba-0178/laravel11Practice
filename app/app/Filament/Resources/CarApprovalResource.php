<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Constants\CarStatus;
use App\Filament\Resources\CarApprovalResource\Pages\ListCarApprovals;
use App\Filament\Resources\CarApprovalResource\Pages\ViewCarApproval;
use App\Infrastructure\Eloquent\User\StkCar;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use App\Constants\NavigationGroup;
use App\Constants\NavigationSort;

class CarApprovalResource extends Resource
{
    protected static ?string $model             = StkCar::class;
    protected static ?string $navigationIcon    = 'heroicon-o-clipboard-document-check';
    protected static ?string $navigationGroup   = NavigationGroup::ADMIN_GROUP->value;
    protected static ?int    $navigationSort    = NavigationSort::CAR_APPROVAL->value;
    protected static ?string $pluralModelLabel  = '車両承認';
    protected static ?string $modelLabel        = '車両承認';

    public static function canAccess(): bool
    {
        return in_array(auth()->user()?->role, ['super', 'admin']);
    }

    public static function getNavigationBadge(): ?string
    {
        return (string) StkCar::where('status', CarStatus::PENDING)->count() ?: null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('管理番号')
                    ->sortable()
                    ->formatStateUsing(fn ($state) => "#{$state}"),

                Tables\Columns\TextColumn::make('series.series_name')
                    ->label('車体名'),

                Tables\Columns\TextColumn::make('dealer.name')
                    ->label('ディーラー'),

                Tables\Columns\TextColumn::make('model_year')
                    ->label('年式'),

                Tables\Columns\TextColumn::make('price')
                    ->label('価格')
                    ->money('JPY'),

                Tables\Columns\TextColumn::make('status')
                    ->label('ステータス')
                    ->badge()
                    ->color(fn (string $state) => CarStatus::COLORS[$state] ?? 'gray')
                    ->formatStateUsing(fn (string $state) => CarStatus::LABELS[$state] ?? $state),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('申請日時')
                    ->dateTime('Y/m/d H:i')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->recordUrl(fn (StkCar $record) => ViewCarApproval::getUrl(['record' => $record]));
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCarApprovals::route('/'),
            'view'  => ViewCarApproval::route('/{record}'),
        ];
    }
}