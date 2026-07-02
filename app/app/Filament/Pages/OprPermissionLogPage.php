<?php

declare(strict_types=1);

namespace App\Filament\Pages;

use App\Constants\RoleConstants;
use App\Infrastructure\Eloquent\Opr\OprPermissionLog;
use App\Infrastructure\Eloquent\Opr\OprResourcePermission;
use App\Models\User;
use Filament\Pages\Page;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class OprPermissionLogPage extends Page implements HasTable
{
    use InteractsWithTable;

    protected static ?string $navigationIcon  = 'heroicon-o-clock';
    protected static string  $view            = 'filament.pages.opr-permission-log';
    protected static ?string $navigationGroup = 'システム';
    protected static ?string $title           = '権限変更履歴';
    protected static ?int    $navigationSort  = 901;

    public static function canAccess(): bool
    {
        return auth()->user()?->role === RoleConstants::SUPER;
    }

    public function table(Table $table): Table
    {
        $labelMap = OprResourcePermission::pluck('resource_label', 'resource_key')->toArray();
        $users    = User::pluck('name', 'id')->toArray();

        return $table
            ->query(OprPermissionLog::query()->orderByDesc('created_at'))
            ->columns([
                TextColumn::make('created_at')
                    ->label('変更日時')
                    ->dateTime('Y/m/d H:i')
                    ->sortable(),
                TextColumn::make('resource_key')
                    ->label('機能')
                    ->getStateUsing(fn(OprPermissionLog $record) =>
                        ($labelMap[$record->resource_key] ?? $record->resource_key)
                        . "\n" . $record->resource_key
                    )
                    ->wrap(),
                TextColumn::make('changed_by')
                    ->label('変更者')
                    ->getStateUsing(fn(OprPermissionLog $record) =>
                        ($users[$record->changed_by] ?? 'ID:' . $record->changed_by)
                        . '（' . (RoleConstants::LABELS[$record->changed_by_role] ?? $record->changed_by_role) . '）'
                    ),
                TextColumn::make('before_roles')
                    ->label('変更前')
                    ->getStateUsing(fn(OprPermissionLog $record) =>
                        $this->formatRoles($record->before_roles)
                    )
                    ->color('danger')
                    ->wrap(),
                TextColumn::make('after_roles')
                    ->label('変更後')
                    ->getStateUsing(fn(OprPermissionLog $record) =>
                        $this->formatRoles($record->after_roles)
                    )
                    ->color('success')
                    ->wrap(),
            ])
            ->filters([
                SelectFilter::make('resource_key')
                    ->label('機能')
                    ->options(
                        OprPermissionLog::query()
                            ->distinct()
                            ->pluck('resource_key', 'resource_key')
                            ->mapWithKeys(fn($key) => [$key => ($labelMap[$key] ?? $key)])
                            ->toArray()
                    ),
                SelectFilter::make('changed_by_role')
                    ->label('変更者ロール')
                    ->options(RoleConstants::LABELS),
            ], FiltersLayout::AboveContent)
            ->deferFilters()
            ->hiddenFilterIndicators()
            ->filtersApplyAction(fn(Action $action) => $action->label('適用'))
            ->defaultSort('created_at', 'desc')
            ->defaultPaginationPageOption(50)
            ->paginationPageOptions([25, 50, 100]);
    }

    private function formatRoles(?array $roles): string
    {
        if (empty($roles)) {
            return '（なし）';
        }
        return implode('・', array_map(
            fn($r) => RoleConstants::LABELS[$r] ?? $r,
            $roles
        ));
    }
}
