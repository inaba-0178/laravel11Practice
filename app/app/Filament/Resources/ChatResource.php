<?php

declare(strict_types=1);

namespace App\Filament\Resources;

use App\Constants\NavigationGroup;
use App\Constants\NavigationSort;
use App\Domain\Shared\Constants\UserType;
use App\Filament\Resources\ChatResource\Pages\ListChats;
use App\Filament\Resources\ChatResource\Pages\ViewChat;
use App\Infrastructure\Eloquent\User\Room;
use App\Infrastructure\Eloquent\User\UsrUser;
use App\Models\User;
use Filament\Resources\Resource;
use App\Filament\Concerns\HasResourcePermission;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use App\Infrastructure\Eloquent\User\Message;
use Filament\Tables\Columns\TextColumn;

class ChatResource extends Resource
{
    use HasResourcePermission;
    protected static ?string $model            = Room::class;
    protected static ?string $navigationIcon   = 'heroicon-o-chat-bubble-left-right';
    protected static ?string $navigationGroup  = NavigationGroup::DEALER_GROUP->value;
    protected static ?int    $navigationSort   = NavigationSort::CHAT->value;
    protected static ?string $pluralModelLabel = 'チャット';
    protected static ?string $modelLabel       = 'チャット';

    private const CREATABLE_ROLES = ['dealer', 'dealer_staff'];


    public static function canCreate(): bool
    {
        return in_array(Auth::user()?->role, self::CREATABLE_ROLES);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->query(self::buildQuery())
            ->columns([
                TextColumn::make('name')
                    ->label('ルーム名')
                    ->default('ダイレクトメッセージ'),

                TextColumn::make('unread_badge')
                    ->label('')
                    ->getStateUsing(function (Room $record) {
                        $userId = (string) Auth::id();
                        $count = Message::where('user_type', UserType::MEMBER)
                            ->where('room_id', $record->id)
                            ->whereDoesntHave('messageReads', fn($q) => $q
                                ->where('user_id', $userId)
                                ->where('user_type', UserType::STAFF)
                            )
                            ->count();
                        return $count > 0 ? 'NEW ' . $count : null;
                    })
                    ->badge()
                    ->color('danger'),

                TextColumn::make('messages_count')
                    ->label('メッセージ数')
                    ->counts('messages'),

                TextColumn::make('latest_message')
                    ->label('最後のメッセージ')
                    ->getStateUsing(fn (Room $record) => 'メッセージ：' . ($record->messages->first()?->message ?? '-'))
                    ->description(function (Room $record) {
                        $msg = $record->messages->first();
                        if (!$msg) return null;
                        $sender = $msg->user_type === UserType::STAFF
                            ? User::find($msg->user_id)
                            : UsrUser::find($msg->user_id);
                        return '送信者：' . UserType::getDisplayName($sender, $msg->user_type);
                    })
                    ->limit(40),

                TextColumn::make('updated_at')
                    ->label('最終更新')
                    ->dateTime('Y/m/d H:i')
                    ->sortable(),
            ])
            ->defaultSort('updated_at', 'desc')
            ->poll('10s')
            ->recordUrl(fn (Room $record) => ViewChat::getUrl(['record' => $record]));
    }

    public static function getPages(): array
    {
        return [
            'index' => ListChats::route('/'),
            'view'  => ViewChat::route('/{record}'),
        ];
    }

    private static function buildQuery(): Builder
    {
        $role   = Auth::user()?->role;
        $query  = Room::query()
            ->where('is_active', 1)
            ->with(['messages' => fn ($q) => $q->latest()->limit(1), 'roomUsers']);

        // super/admin はなりすまし中のみ絞り込み
        if (in_array($role, ['super', 'admin'])) {
            $dealerId = Auth::user()?->getEffectiveDealerId();
            if ($dealerId) {
                return $query->whereHas('roomUsers', function (Builder $q) use ($dealerId) {
                    $q->where('user_type', UserType::STAFF)
                      ->whereIn('user_id', \App\Models\User::where('dealer_id', $dealerId)->pluck('id')->map(fn($id) => (string) $id));
                });
            }
            return $query;
        }

        // dealer/dealer_staff は自分が参加しているルームのみ
        return $query->whereHas('roomUsers', function (Builder $q) {
            $q->where('user_id', (string) Auth::id())
              ->where('user_type', UserType::STAFF);
        });
    }
}