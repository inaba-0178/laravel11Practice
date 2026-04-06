<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Filament\Resources\LoanSettingResource\Pages\ListLoanSettings;
use App\Filament\Resources\LoanSettingResource\Pages\ViewLoanSetting;
use App\Infrastructure\Eloquent\User\StkCarDealer;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class LoanSettingResource extends Resource
{
    protected static ?string $model           = StkCarDealer::class;
    protected static ?string $navigationIcon  = 'heroicon-o-banknotes';
    protected static ?string $navigationGroup = '管理者メニュー';
    protected static ?string $pluralModelLabel = 'ローン設定申請';

    public static function canAccess(): bool
    {
        return in_array(auth()->user()?->role, ['super', 'admin']);
    }

    public static function getNavigationBadge(): ?string
    {
        $count = StkCarDealer::whereNotNull('loan_setting_requested_at')
            ->where('loan_setting_enabled', 0)
            ->whereNull('loan_setting_approved_at')
            ->whereNull('loan_setting_rejected_reason')
            ->count();

        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('ディーラー名')
                    ->searchable(),

                Tables\Columns\TextColumn::make('requestedBy.name')
                    ->label('申請担当者')
                    ->default('-'),

                Tables\Columns\TextColumn::make('loan_setting_reason')
                    ->label('申請理由')
                    ->limit(30)
                    ->default('-'),

                Tables\Columns\TextColumn::make('loan_setting_requested_at')
                    ->label('申請日時')
                    ->dateTime('Y/m/d H:i')
                    ->default('-'),

                Tables\Columns\TextColumn::make('loan_setting_enabled')
                    ->label('ステータス')
                    ->badge()
                    ->formatStateUsing(fn ($state, $record) => match(true) {
                        $record->loan_setting_enabled == 1                                     => '許可済み',
                        !empty($record->loan_setting_rejected_reason)                          => '拒否済み',
                        !empty($record->loan_setting_requested_at)
                            && empty($record->loan_setting_approved_at)                        => '申請中',
                        $record->loan_setting_enabled == 0
                            && !empty($record->loan_setting_approved_at)
                            && empty($record->loan_setting_rejected_reason)                    => '承認取消',
                        default                                                                => '未申請',
                    })
                    ->color(fn ($state, $record) => match(true) {
                        $record->loan_setting_enabled == 1                                     => 'success',
                        !empty($record->loan_setting_rejected_reason)                          => 'danger',
                        !empty($record->loan_setting_requested_at)
                            && empty($record->loan_setting_approved_at)                        => 'warning',
                        $record->loan_setting_enabled == 0
                            && !empty($record->loan_setting_approved_at)
                            && empty($record->loan_setting_rejected_reason)                    => 'gray',
                        default                                                                => 'gray',
                    }),
            ])
            ->modifyQueryUsing(fn ($query) => $query->whereNotNull('loan_setting_requested_at'))
            ->defaultSort('loan_setting_requested_at', 'desc')
            ->recordUrl(fn (StkCarDealer $record) => ViewLoanSetting::getUrl(['record' => $record]));
    }

    public static function getPages(): array
    {
        return [
            'index' => ListLoanSettings::route('/'),
            'view'  => ViewLoanSetting::route('/{record}'),
        ];
    }
}