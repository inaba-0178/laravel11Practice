<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Constants\NavigationGroup;
use App\Constants\NavigationSort;
use App\Constants\Role\RoleManagement;
use App\Filament\Resources\MstVersionResource\Pages\ListMstVersions;
use App\Filament\Resources\MstVersionResource\Pages\ViewMstVersion;
use App\Infrastructure\Eloquent\Mst\MstVersion;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Actions\Action;
use Filament\Tables\Enums\ActionsPosition;
use Illuminate\Support\Facades\Auth;

class MstVersionResource extends Resource
{
    protected static ?string $model            = MstVersion::class;
    protected static ?string $navigationIcon   = 'heroicon-o-archive-box';
    protected static ?string $navigationGroup  = NavigationGroup::MST_UPDATE_GROUP->value;
    protected static ?int    $navigationSort   = NavigationSort::MST_VERSION->value;
    protected static ?string $pluralModelLabel = 'バージョン管理';
    protected static ?string $modelLabel       = 'バージョン';

    public static function canAccess(): bool
    {
        return in_array(Auth::user()?->role, RoleManagement::MST_OPERATOR_ROLES);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),

                TextColumn::make('version')
                    ->label('バージョン')
                    ->sortable(),

                TextColumn::make('description')
                    ->label('説明')
                    ->limit(50),

                TextColumn::make('status')
                    ->label('ステータス')
                    ->badge()
                    ->color(fn (string $state) => match($state) {
                        'draft'    => 'gray',
                        'pending'  => 'warning',
                        'approved' => 'info',
                        'active'   => 'success',
                        'archived' => 'danger',
                        default    => 'gray',
                    })
                    ->formatStateUsing(fn (string $state) => match($state) {
                        'draft'    => '下書き',
                        'pending'  => '承認待ち',
                        'approved' => '承認済み',
                        'active'   => '有効',
                        'archived' => 'アーカイブ',
                        default    => $state,
                    }),

                TextColumn::make('uploadedBy.name')
                    ->label('アップロード者'),

                TextColumn::make('uploaded_at')
                    ->label('アップロード日時')
                    ->dateTime('Y/m/d H:i')
                    ->sortable(),

                TextColumn::make('approvedBy.name')
                    ->label('承認者'),

                TextColumn::make('approved_at')
                    ->label('承認日時')
                    ->dateTime('Y/m/d H:i'),

                TextColumn::make('activatedBy.name')
                    ->label('有効化者'),

                TextColumn::make('activated_at')
                    ->label('有効化日時')
                    ->dateTime('Y/m/d H:i'),
            ])
            ->defaultSort('id', 'desc')
            ->actions([
                Action::make('view')
                    ->label('詳細')
                    ->url(fn (MstVersion $record) => ViewMstVersion::getUrl(['record' => $record->id])),
            ], position: ActionsPosition::BeforeColumns);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListMstVersions::route('/'),
            'view'  => ViewMstVersion::route('/{record}'),
        ];
    }
}