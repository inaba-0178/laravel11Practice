<?php

declare(strict_types=1);

namespace App\Filament\Pages;

use App\Constants\RoleConstants;
use App\Infrastructure\Eloquent\Log\LogAuth;
use Filament\Pages\Page;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class OprLoginLogPage extends Page implements HasTable
{
    use InteractsWithTable;

    protected static ?string $navigationIcon  = 'heroicon-o-arrow-right-end-on-rectangle';
    protected static string  $view            = 'filament.pages.opr-login-log';
    protected static ?string $navigationGroup = 'システム';
    protected static ?string $title           = 'ログインログ';
    protected static ?int    $navigationSort  = 907;

    public static function canAccess(): bool
    {
        return auth()->user()?->role === RoleConstants::SUPER;
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                LogAuth::query()
                    ->where('action', 'login')
                    ->where('operator_type', 'admin')
                    ->orderByDesc('operation_at')
            )
            ->columns([
                TextColumn::make('operation_at')
                    ->label('日時')
                    ->dateTime('Y/m/d H:i:s')
                    ->sortable(),
                TextColumn::make('operator_name')
                    ->label('ユーザー名')
                    ->searchable(),
                TextColumn::make('result')
                    ->label('結果')
                    ->badge()
                    ->color(fn(string $state) => match ($state) {
                        'success' => 'success',
                        'failed'  => 'danger',
                        default   => 'gray',
                    })
                    ->formatStateUsing(fn(string $state) => match ($state) {
                        'success' => '成功',
                        'failed'  => '失敗',
                        default   => $state,
                    }),
                TextColumn::make('ip_address')
                    ->label('IPアドレス')
                    ->searchable()
                    ->copyable()
                    ->fontFamily('mono'),
                TextColumn::make('user_agent')
                    ->label('ブラウザ')
                    ->limit(60)
                    ->tooltip(fn($record) => $record->user_agent),
            ])
            ->filters([
                Filter::make('date')
                    ->label('日付')
                    ->form([
                        \Filament\Forms\Components\DatePicker::make('from')->label('開始日'),
                        \Filament\Forms\Components\DatePicker::make('until')->label('終了日'),
                    ])
                    ->query(fn(Builder $query, array $data) => $query
                        ->when($data['from'],  fn($q) => $q->whereDate('operation_at', '>=', $data['from']))
                        ->when($data['until'], fn($q) => $q->whereDate('operation_at', '<=', $data['until']))
                    ),
                SelectFilter::make('result')
                    ->label('結果')
                    ->options(['success' => '成功', 'failed' => '失敗']),
            ], \Filament\Tables\Enums\FiltersLayout::AboveContent)
            ->deferFilters()
            ->hiddenFilterIndicators()
            ->filtersApplyAction(fn(\Filament\Tables\Actions\Action $action) => $action->label('適用'))
            ->defaultPaginationPageOption(50)
            ->paginationPageOptions([25, 50, 100]);
    }
}
