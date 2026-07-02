<?php

declare(strict_types=1);

namespace App\Filament\Pages;

use App\Constants\RoleConstants;
use App\Infrastructure\Eloquent\Opr\OprResourcePermission;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;

class OprRolePermissionViewPage extends Page implements HasTable, HasForms
{
    use InteractsWithTable;
    use InteractsWithForms;

    protected static ?string $navigationIcon  = 'heroicon-o-magnifying-glass';
    protected static string  $view            = 'filament.pages.opr-role-permission-view';
    protected static ?string $navigationGroup = 'システム';
    protected static ?string $title           = 'ロール別アクセス確認';
    protected static ?int    $navigationSort  = 902;

    public static function canAccess(): bool
    {
        return auth()->user()?->role === RoleConstants::SUPER;
    }

    public ?string $selectedRole = null;

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('selectedRole')
                    ->label('確認するロール')
                    ->options(collect(RoleConstants::LABELS)->except(RoleConstants::SUPER)->toArray())
                    ->placeholder('ロールを選択してください')
                    ->live()
                    ->afterStateUpdated(fn () => $this->resetTable()),
            ])
            ->statePath('');
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                OprResourcePermission::query()
                    ->when(
                        $this->selectedRole,
                        fn($q) => $q->whereJsonContains('allowed_roles', $this->selectedRole)
                                    ->orderBy('resource_group')
                                    ->orderBy('resource_label'),
                        fn($q) => $q->whereRaw('1 = 0')
                    )
            )
            ->columns([
                TextColumn::make('resource_group')
                    ->label('グループ')
                    ->badge()
                    ->color('gray')
                    ->sortable(),
                TextColumn::make('resource_label')
                    ->label('機能名')
                    ->sortable(),
                TextColumn::make('allowed_roles')
                    ->label('許可ロール（全体）')
                    ->getStateUsing(fn(OprResourcePermission $record) => implode('・', array_map(
                        fn($r) => RoleConstants::LABELS[$r] ?? $r,
                        $record->allowed_roles ?? []
                    ))),
            ])
            ->paginated(false)
            ->heading(
                $this->selectedRole
                    ? (RoleConstants::LABELS[$this->selectedRole] ?? $this->selectedRole) . ' がアクセスできる機能'
                    : 'ロールを選択してください'
            );
    }
}
