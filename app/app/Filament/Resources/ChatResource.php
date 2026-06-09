<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Constants\NavigationGroup;
use App\Constants\NavigationSort;
use App\Filament\Resources\ChatResource\Pages\ListChats;
use App\Filament\Resources\ChatResource\Pages\ViewChat;
use App\Infrastructure\Eloquent\User\Room;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class ChatResource extends Resource
{
    protected static ?string $model           = Room::class;
    protected static ?string $navigationIcon  = 'heroicon-o-chat-bubble-left-right';
    protected static ?string $navigationGroup = NavigationGroup::DEALER_GROUP->value;
    protected static ?int    $navigationSort  = NavigationSort::CHAT->value;
    protected static ?string $pluralModelLabel = 'チャット';
    protected static ?string $modelLabel       = 'チャット';

    public static function canAccess(): bool
    {
        return in_array(Auth::user()?->role, ['dealer', 'dealer_staff']);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->query(
                Room::query()
                    ->whereHas('roomUsers', function (Builder $query) {
                        $query->where('user_id', (string) Auth::id())
                              ->where('user_type', 'staff');
                    })
                    ->where('is_active', 1)
                    ->with(['messages' => fn ($q) => $q->latest()->limit(1), 'roomUsers'])
            )
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('ルーム名')
                    ->default('ダイレクトメッセージ'),

                Tables\Columns\TextColumn::make('messages_count')
                    ->label('メッセージ数')
                    ->counts('messages'),

                Tables\Columns\TextColumn::make('latest_message')
                    ->label('最後のメッセージ')
                    ->getStateUsing(fn (Room $record) => $record->messages->first()?->message ?? '-')
                    ->limit(30),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('最終更新')
                    ->dateTime('Y/m/d H:i')
                    ->sortable(),
            ])
            ->defaultSort('updated_at', 'desc')
            ->recordUrl(fn (Room $record) => ViewChat::getUrl(['record' => $record]));
    }

    public static function getPages(): array
    {
        return [
            'index' => ListChats::route('/'),
            'view'  => ViewChat::route('/{record}'),
        ];
    }
}