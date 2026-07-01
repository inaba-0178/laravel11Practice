<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages\ListUsers;
use App\Filament\Resources\UserResource\Pages\CreateUser;
use App\Filament\Resources\UserResource\Pages\EditUser;
use App\Constants\RoleConstants;
use App\Constants\Role\RoleManagement;
use App\Constants\NavigationSort;
use App\Constants\NavigationGroup;
use App\Models\User;
use Filament\Resources\Resource;
use App\Filament\Concerns\HasResourcePermission;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\ActionsPosition;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Pages\UserDetail;
use Filament\Tables\Actions\Action;

class UserResource extends Resource
{
    use HasResourcePermission;
    protected static ?string $model            = User::class;
    protected static ?string $navigationIcon   = 'heroicon-o-user-group';
    protected static ?string $navigationGroup  = NavigationGroup::SYSTEM_GROUP->value;
    protected static ?int    $navigationSort   = NavigationSort::USER_MANAGEMENT->value;
    protected static ?string $pluralModelLabel = 'ユーザー管理';
    protected static ?string $modelLabel       = 'ユーザー';

    public static function canAccess(): bool
    {
        $role = Auth::user()?->role;
        return in_array($role, RoleManagement::USER_MANAGEMENT_ACCESS_ROLES);
    }

    public static function getEloquentQuery(): Builder
    {
        $user       = Auth::user();
        $role       = $user->role;
        $lowerRoles = RoleConstants::getLowerOrEqualRoles($role);
        $query      = parent::getEloquentQuery()->whereIn('role', $lowerRoles);

        if (in_array($role, RoleManagement::DEALER_ROLES)) {
            $query->where('dealer_id', $user->dealer_id);
        }

        return $query;
    }

    public static function form(Form $form): Form
    {
        $user       = Auth::user();
        $lowerRoles = RoleConstants::getLowerOrEqualRoles($user->role);

        $roleOptions = collect(RoleConstants::HIERARCHY)
            ->filter(fn ($level, $role) => in_array($role, $lowerRoles))
            ->mapWithKeys(fn ($level, $role) => [$role => RoleConstants::LABELS[$role] ?? $role])
            ->toArray();

        return $form->schema([
            TextInput::make('name')
                ->label('名前')
                ->required()
                ->maxLength(255),

            TextInput::make('email')
                ->label('メールアドレス')
                ->email()
                ->required()
                ->maxLength(255)
                ->unique(ignoreRecord: true),

            Select::make('role')
                ->label('ロール')
                ->options($roleOptions)
                ->required(),

            TextInput::make('position')
                ->label('役職')
                ->nullable()
                ->maxLength(100),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),

                TextColumn::make('name')
                    ->label('名前')
                    ->sortable(),

                TextColumn::make('email')
                    ->label('メールアドレス'),

                TextColumn::make('role')
                    ->label('ロール')
                    ->formatStateUsing(fn ($state) => RoleConstants::LABELS[$state] ?? $state)
                    ->sortable(),

                TextColumn::make('position')
                    ->label('役職'),

                TextColumn::make('created_at')
                    ->label('作成日時')
                    ->dateTime('Y/m/d H:i')
                    ->sortable(),
            ])
            ->recordUrl(fn (User $record) => UserDetail::getUrl(['id' => $record->id]))
            ->actions([
                Action::make('detail')
                    ->label('詳細')
                    ->url(fn (User $record) => UserDetail::getUrl(['id' => $record->id])),
            ], position: ActionsPosition::BeforeColumns);
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListUsers::route('/'),
            'create' => CreateUser::route('/create'),
            'edit'   => EditUser::route('/{record}/edit'),
        ];
    }
}